<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class ShowFeedbackController extends AbstractShowController
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
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::findOrFail($id);

        return $feedback;
    }
}