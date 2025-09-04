<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class ListFeedbacksController extends AbstractListController
{
    public $serializer = FeedbackSerializer::class;
    
    public $include = ['fromUser', 'toUser'];
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $params = $request->getQueryParams();
        
        $userId = Arr::get($params, 'filter.user');
        $type = Arr::get($params, 'filter.type');
        $sort = Arr::get($params, 'filter.sort', 'newest');
        
        $query = Feedback::query()
            ->with(['fromUser', 'toUser'])  // Eager load relationships
            ->where('to_user_id', $userId)
            ->where('is_approved', true);
        
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
        
        if ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'asc');
        }
        
        return $query->get();
    }
}