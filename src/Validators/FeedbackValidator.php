<?php

namespace HuseyinFiliz\TraderFeedback\Validators;

use Flarum\Foundation\AbstractValidator;

class FeedbackValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'to_user_id' => [
            'required',
            'integer',
            'exists:users,id'
        ],
        'type' => [
            'required',
            'string',
            'in:positive,neutral,negative'
        ],
        'role' => [
            'required',
            'string', 
            'in:buyer,seller,trader'
        ],
        'comment' => [
            'required',
            'string',
            'min:10',
            'max:1000'
        ],
        'discussion_id' => [
            'nullable',
            'integer',
            'exists:discussions,id'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    protected function getRules()
    {
        $rules = $this->rules;
        
        // Settings'i app container'dan alalım
        $settings = app('flarum.settings');
        
        if ($settings) {
            $minLength = (int) $settings->get('huseyinfiliz.traderfeedback.minLength', 10);
            $maxLength = (int) $settings->get('huseyinfiliz.traderfeedback.maxLength', 1000);
            
            $rules['comment'] = [
                'required',
                'string',
                'min:' . $minLength,
                'max:' . $maxLength
            ];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessages()
    {
        return [
            'to_user_id.required' => 'User ID is required.',
            'to_user_id.exists' => 'User does not exist.',
            'type.required' => 'Feedback type is required.',
            'type.in' => 'Invalid feedback type. Must be positive, neutral, or negative.',
            'role.required' => 'Your role in the transaction is required.',
            'role.in' => 'Invalid role. Must be buyer, seller, or trader.',
            'comment.required' => 'Comment is required.',
            'comment.min' => 'Comment must be at least :min characters.',
            'comment.max' => 'Comment must not exceed :max characters.',
            'discussion_id.exists' => 'Discussion does not exist.'
        ];
    }
}