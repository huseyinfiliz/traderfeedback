<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\FeedbackDeleted;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackReportedBlueprint;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RejectReportController implements RequestHandlerInterface
{
    public function __construct(
        protected Dispatcher $events,
        protected NotificationSyncer $notifications
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $actor->assertCan('huseyinfiliz-traderfeedback.moderate');

        $report = FeedbackReport::findOrFail($id);
        $feedback = $report->feedback;

        $report->resolved = true;
        $report->resolved_by_id = $actor->id;
        $report->updated_at = Carbon::now();
        $report->save();

        // Clear report notifications for moderators
        try {
            $this->notifications->sync(new FeedbackReportedBlueprint($report), []);
        } catch (\Exception $e) {
            // Ignore if notification cannot be synced
        }

        if ($feedback) {
            $toUserId = (int) $feedback->to_user_id;

            $this->events->dispatch(new FeedbackDeleted($feedback, $actor));

            $feedback->delete();

            StatsService::updateUserStats($toUserId);
        }

        return new JsonResponse([
            'success' => true,
            'data'    => FeedbackSerializer::report($report, $actor),
        ]);
    }
}
