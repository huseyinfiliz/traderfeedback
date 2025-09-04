<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Validators\FeedbackValidator;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;

class CreateFeedbackController extends AbstractCreateController
{
    public $serializer = FeedbackSerializer::class;

    protected $validator;

    public function __construct(FeedbackValidator $validator)
    {
        $this->validator = $validator;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data', []);
        
        // Validate the request data
        $this->validator->assertValid($data);
        
        // Check if user is trying to give feedback to themselves
        if ($actor->id == Arr::get($data, 'attributes.to_user_id')) {
            throw new ValidationException(['to_user_id' => 'You cannot give feedback to yourself']);
        }
        
        // Create the feedback
        $feedback = Feedback::build([
            'from_user_id' => $actor->id,
            'to_user_id' => Arr::get($data, 'attributes.to_user_id'),
            'type' => Arr::get($data, 'attributes.type'),
            'comment' => Arr::get($data, 'attributes.comment'),
            'role' => Arr::get($data, 'attributes.role'),
            'transaction_id' => Arr::get($data, 'attributes.transaction_id'),
            'is_approved' => !$this->settings->get('huseyinfiliz.traderfeedback.requireApproval', false)
        ]);
        
        $feedback->save();
        
        // Update user stats if feedback is approved
        if ($feedback->is_approved) {
            $feedback->updateStats();
        }
        
        // Send notification to the recipient
        $recipient = $feedback->toUser;
        $recipient->notify(new NewFeedbackBlueprint($feedback));
        
        return $feedback;
    }
}