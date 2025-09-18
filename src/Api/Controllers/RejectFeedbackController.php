<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use Flarum\User\User;

class RejectFeedbackController extends AbstractShowController
{
    public $serializer = FeedbackSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');

        $feedback = Feedback::findOrFail($id);
        
        // Reddeden kişiyi kaydet
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // İlişkileri yükle
        $feedback->load(['fromUser', 'toUser']);

        // BİLDİRİM GÖNDER - Silmeden önce!
        try {
            $fromUser = User::find($feedback->from_user_id);
            
            if ($fromUser && $fromUser->id !== $actor->id) {
                $notifications = app(NotificationSyncer::class);
                $blueprint = new FeedbackRejectedBlueprint($feedback);
                $notifications->sync($blueprint, [$fromUser]);
                
                // Debug log
                app('log')->info('FeedbackRejected notification sent', [
                    'feedback_id' => $feedback->id,
                    'to_user' => $fromUser->id,
                    'from_user' => $actor->id
                ]);
            }
        } catch (\Exception $e) {
            app('log')->error('FeedbackRejected notification error: ' . $e->getMessage());
        }

        // Şimdi sil
        $feedback->delete();

        return $feedback;
    }
}