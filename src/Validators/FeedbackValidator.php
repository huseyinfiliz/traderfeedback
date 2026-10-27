<?php

namespace HuseyinFiliz\TraderFeedback\Validators;

use Flarum\Foundation\AbstractValidator;
use Flarum\Locale\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Validation\Factory;

class FeedbackValidator extends AbstractValidator
{
    public function __construct(Factory $validator, TranslatorInterface $translator, protected SettingsRepositoryInterface $settings)
    {
        parent::__construct($validator, $translator);
    }

    /**
     * {@inheritdoc}
     */
    protected array $rules = [
        'to_user_id' => [
            'required',
            'integer',
            'exists:users,id',
        ],
        'type' => [
            'required',
            'string',
            'in:positive,neutral,negative',
        ],
        'role' => [
            'required',
            'string',
            'in:buyer,seller,trader',
        ],
        'comment' => [
            'required',
            'string',
            'min:10',
            'max:1000',
        ],
        'discussion_id' => [
            'nullable',
            'integer',
            'exists:discussions,id',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected function getRules(): array
    {
        $rules = $this->rules;

        $minLength = max(1, (int) $this->settings->get('huseyinfiliz.traderfeedback.minLength', 10));
        $maxLength = max($minLength, (int) $this->settings->get('huseyinfiliz.traderfeedback.maxLength', 1000));

        $rules['comment'] = [
            'required',
            'string',
            'min:'.$minLength,
            'max:'.$maxLength,
        ];

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessages(): array
    {
        return [
            'to_user_id.required'  => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.user_required'),
            'to_user_id.exists'    => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.user_not_found'),
            'type.in'              => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.invalid_type'),
            'role.in'              => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.invalid_role'),
            'discussion_id.exists' => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.discussion_not_found'),
        ];
    }
}
