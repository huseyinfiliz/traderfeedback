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
    
    public $limit = 20;
    public $maxLimit = 50;
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $params = $request->getQueryParams();
        
        $userId = Arr::get($params, 'filter.user');
        $type = Arr::get($params, 'filter.type');
        $sort = Arr::get($params, 'filter.sort', 'newest');
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        
        $query = Feedback::query()
            ->with(['fromUser', 'toUser']) // Eager load relationships
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
        
        // Paginate
        $results = $query->skip($offset)->take($limit + 1)->get();
        $hasMoreResults = $results->count() > $limit;
        
        if ($hasMoreResults) {
            $results->pop();
        }
        
        // Add pagination info
        $document->addPaginationLinks(
            $request->getUri()->getPath(),
            $request->getQueryParams(),
            $offset,
            $limit,
            $hasMoreResults ? null : 0
        );
        
        return $results;
    }
}