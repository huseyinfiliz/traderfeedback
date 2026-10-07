<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeleteFeedbackController extends AbstractDeleteController
{
    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        $actor->assertCan('delete', $feedback);

        $toUserId = (int) $feedback->to_user_id;
        $isApproved = (bool) $feedback->is_approved;

        $feedback->delete();

        if ($isApproved) {
            StatsService::updateUserStats($toUserId);
        }
    }
}
