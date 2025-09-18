<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use Flarum\Notification\NotificationSyncer;

/**
 * Reject a feedback entry and notify the feedback author.
 *
 * In earlier versions the rejection controller did not record who rejected
 * the feedback. Consequently, the FeedbackRejectedBlueprint would not
 * populate the from_user_id, leading to null values in the notifications
 * table. To fix this we now assign the approving moderator to
 * approved_by_id (there is no rejected_by_id column) before dispatching
 * the notification. This ensures the blueprint can correctly resolve the
 * from user and fill the notification data.
 */
class RejectFeedbackController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    /**
     * Inject the notification syncer.
     *
     * @param NotificationSyncer $notifications
     */
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');

        // Load the feedback with its relationships so the blueprint can
        // populate notification data.
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);

        // Record who rejected the feedback using the approved_by_id column.
        // There is no rejected_by_id column, so we reuse approved_by_id.
        $feedback->approved_by_id = $actor->id;
        $feedback->save();

        // Notify the feedback author before deleting the record. The blueprint
        // will pick up approved_by_id to set from_user_id correctly and
        // include toUser in its data payload.
        if ($feedback->fromUser && $feedback->fromUser->id !== $actor->id) {
            $this->notifications->sync(
                new FeedbackRejectedBlueprint($feedback),
                [$feedback->fromUser]
            );
        }

        // Delete the feedback entry.
        $feedback->delete();

        return $feedback;
    }
}