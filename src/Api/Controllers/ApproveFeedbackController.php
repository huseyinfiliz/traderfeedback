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
        
        // Feedback'i relationship'leriyle birlikte yükle
        $feedback = Feedback::with(['fromUser', 'toUser'])->findOrFail($id);
        
        // Sadece onayla ve event fırlat
        $feedback->is_approved = true;
        $feedback->approved_by_id = $actor->id;
        $feedback->save();
        
        // Event fırlat - gerisi listener'da halledilecek
        event(new FeedbackUpdated($feedback, $actor));
        
        return $feedback;
    }
}