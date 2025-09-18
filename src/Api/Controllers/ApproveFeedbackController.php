<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;

/**
 * Controller that approves a feedback entry.
 *
 * When a moderator approves a feedback, the is_approved flag and
 * approved_by_id are updated and a FeedbackUpdated event is fired. The
 * FeedbackUpdatedListener will handle sending the appropriate notification.
 */
class ApproveFeedbackController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');

        // Load the feedback with its relationships so we can approve it.
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);

        // Mark as approved and record who approved it.
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();

        // Fire the event - the listener will handle sending the notification
        // and updating statistics.
        event(new FeedbackUpdated($feedback, $actor));

        return $feedback;
    }
}