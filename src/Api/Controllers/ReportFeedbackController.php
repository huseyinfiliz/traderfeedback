<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Locale\TranslatorInterface;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReportFeedbackController implements RequestHandlerInterface
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();

        $body = $request->getParsedBody();
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        // Check if user can report this feedback
        $actor->assertCan('report', $feedback);

        // Check if already reported by this user
        $existingReport = FeedbackReport::where('feedback_id', $feedback->id)
            ->where('user_id', $actor->id)
            ->where('resolved', false)
            ->first();

        if ($existingReport) {
            throw new ValidationException([
                'feedback' => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.already_reported'),
            ]);
        }

        // Rate Limit Check: Max 1 report per minute
        $recentReport = FeedbackReport::where('user_id', $actor->id)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->exists();

        if ($recentReport) {
            throw new ValidationException([
                'rate_limit' => $this->translator->trans(
                    'huseyinfiliz-traderfeedback.api.validation.rate_limit_report',
                    ['seconds' => 60]
                ),
            ]);
        }

        $reason = Arr::get($body, 'data.attributes.reason')
            ?? Arr::get($body, 'data.reason')
            ?? Arr::get($body, 'reason')
            ?? 'No reason provided';

        $sanitizedReason = mb_substr(trim(strip_tags((string) $reason)), 0, 1000);
        if (empty($sanitizedReason)) {
            $sanitizedReason = 'No reason provided';
        }

        // Create the report
        $report = new FeedbackReport();
        $report->user_id = $actor->id;
        $report->feedback_id = $feedback->id;
        $report->reason = $sanitizedReason;
        $report->resolved = false;
        $report->save();

        $report->load(['reporter', 'feedback.fromUser', 'feedback.toUser']);

        return new JsonResponse([
            'success' => true,
            'data'    => FeedbackSerializer::report($report, $actor),
        ], 201);
    }
}
