<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Locale\TranslatorInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackReportedBlueprint;
use HuseyinFiliz\TraderFeedback\Services\ModeratorNotificationSyncer;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReportFeedbackController implements RequestHandlerInterface
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected ModeratorNotificationSyncer $notifications,
        protected ConnectionInterface $db
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();

        $body = $request->getParsedBody();
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        $actor->assertCan('view', $feedback);
        $actor->assertCan('report', $feedback);

        $reason = Arr::get($body, 'data.attributes.reason')
            ?? Arr::get($body, 'data.reason')
            ?? Arr::get($body, 'reason')
            ?? '';

        $sanitizedReason = is_string($reason) ? trim(strip_tags($reason)) : '';
        if ($sanitizedReason === '' || mb_strlen($sanitizedReason) > 1000) {
            throw new ValidationException([
                'reason' => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.invalid_report_reason'),
            ]);
        }

        $report = $this->db->transaction(function () use ($actor, $feedback, $sanitizedReason) {
            User::whereKey($actor->id)->select('id')->lockForUpdate()->firstOrFail();

            // Reports are unique per user and feedback, including resolved reports.
            if (FeedbackReport::where('feedback_id', $feedback->id)->where('user_id', $actor->id)->exists()) {
                throw new ValidationException([
                    'feedback' => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.already_reported'),
                ]);
            }
            if (FeedbackReport::where('user_id', $actor->id)->where('created_at', '>', Carbon::now()->subMinute())->exists()) {
                throw new ValidationException([
                    'rate_limit' => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.rate_limit_report', ['seconds' => 60]),
                ]);
            }

            $report = new FeedbackReport();
            $report->user_id = $actor->id;
            $report->feedback_id = $feedback->id;
            $report->reason = $sanitizedReason;
            $report->resolved = false;
            $report->save();

            return $report;
        }, 3);

        $report->load(['reporter', 'feedback.fromUser', 'feedback.toUser']);

        // Notify moderators about the report
        $this->notifications->syncModerators(new FeedbackReportedBlueprint($report), $actor->id);

        return new JsonResponse([
            'success' => true,
            'data'    => FeedbackSerializer::report($report, $actor),
        ], 201);
    }
}
