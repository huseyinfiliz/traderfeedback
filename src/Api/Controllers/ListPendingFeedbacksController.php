<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class ListPendingFeedbacksController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['fromUser', 'toUser'];

    /**
     * {@inheritdoc}
     */
    public $limit = 20;

    /**
     * {@inheritdoc}
     */
    public $maxLimit = 50;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        
        $actor->assertCan('moderate', 'huseyinfiliz-traderfeedback');

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);

        $results = Feedback::where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit + 1)
            ->get();

        $hasMoreResults = $results->count() > $limit;

        if ($hasMoreResults) {
            $results->pop();
        }

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
