<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Events\ReportDismissed;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackReportedBlueprint;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DismissReportController implements RequestHandlerInterface
{
    public function __construct(
        protected NotificationSyncer $notifications,
        protected Dispatcher $events
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $actor->assertCan('huseyinfiliz-traderfeedback.moderate');

        $report = FeedbackReport::findOrFail($id);

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

        // Dispatch event for audit log
        $this->events->dispatch(new ReportDismissed($report, $actor));

        return new JsonResponse([
            'success' => true,
            'data'    => FeedbackSerializer::report($report, $actor),
        ]);
    }
}
