<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowFeedbackController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['fromUser', 'toUser', 'discussion'];

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        $actor->assertCan('view', $feedback);

        return $feedback;
    }
}
