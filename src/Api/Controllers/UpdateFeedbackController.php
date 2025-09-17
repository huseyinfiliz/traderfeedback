<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;

class UpdateFeedbackController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = FeedbackSerializer::class;

    /**
     * @var FeedbackValidator
     */
    protected $validator;

    /**
     * @param FeedbackValidator $validator
     */
    public function __construct(FeedbackValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $feedback = Feedback::findOrFail($id);
        
        $actor->assertCan('edit', $feedback);
        
        // Only validate fields that are being updated
        $validationData = [];
        
        if (isset($data['type'])) {
            $validationData['type'] = $data['type'];
            $feedback->type = $data['type'];
        }
        
        if (isset($data['comment'])) {
            $validationData['comment'] = $data['comment'];
            $feedback->comment = $data['comment'];
        }
        
        if (isset($data['role'])) {
            $validationData['role'] = $data['role'];
            $feedback->role = $data['role'];
        }
        
        if (isset($data['transaction_id'])) {
            $validationData['transaction_id'] = $data['transaction_id'];
            $feedback->transaction_id = $data['transaction_id'];
        }
        
        // Only validate if there are fields to validate
        if (!empty($validationData)) {
            // For partial updates, only validate the fields being updated
            $this->validator->assertValid($validationData);
        }
        
        $feedback->save();
        
        return $feedback;
    }
}