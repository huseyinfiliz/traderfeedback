<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @TODO: Remove this in favor of one of the API resource classes that were added.
 *      Or extend an existing API Resource to add this to.
 *      Or use a vanilla RequestHandlerInterface controller.
 *      @link https://docs.flarum.org/2.x/extend/api#endpoints
 */
class DeleteFeedbackController extends AbstractDeleteController
{
    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request): void
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
