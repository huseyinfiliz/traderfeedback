<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Events\FeedbackDeleted;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeleteFeedbackController implements RequestHandlerInterface
{
    public function __construct(protected Dispatcher $events)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        $actor->assertCan('delete', $feedback);

        $toUserId = (int) $feedback->to_user_id;
        $isApproved = (bool) $feedback->is_approved;

        $feedback->delete();

        $this->events->dispatch(new FeedbackDeleted($feedback, $actor));

        if ($isApproved) {
            StatsService::updateUserStats($toUserId);
        }

        return new EmptyResponse(204);
    }
}
