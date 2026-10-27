<?php

namespace HuseyinFiliz\TraderFeedback\Services;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackGuard
{
    public function __construct(
        protected SettingsRepositoryInterface $settings,
        protected TranslatorInterface $translator
    ) {
    }

    public function assertEligible(User $actor): void
    {
        $minDays = (int) $this->settings->get('huseyinfiliz.traderfeedback.minDays', 0);
        if ($minDays > 0 && (!$actor->joined_at || $actor->joined_at->diffInDays(Carbon::now(), true) < $minDays)) {
            $this->fail('user', 'min_days', ['days' => $minDays]);
        }

        $minPosts = (int) $this->settings->get('huseyinfiliz.traderfeedback.minPosts', 0);
        if ($minPosts > (int) $actor->comment_count) {
            $this->fail('user', 'min_posts', ['posts' => $minPosts]);
        }
    }

    public function assertType(string $type): void
    {
        if ($type === Feedback::TYPE_NEGATIVE && !(bool) $this->settings->get('huseyinfiliz.traderfeedback.allowNegative', true)) {
            $this->fail('type', 'negative_not_allowed');
        }
    }

    public function discussionId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value) && preg_match('~/d/(\d+)(?:\D|$)~', $value, $matches)) {
            $value = $matches[1];
        }

        if ((!is_int($value) && !is_string($value)) || !ctype_digit((string) $value) || (int) $value <= 0) {
            $this->fail('discussion_id', 'discussion_not_found');
        }

        return (int) $value;
    }

    public function assertDiscussion(User $actor, ?int $discussionId): void
    {
        $onlyLocked = (bool) $this->settings->get('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked', false);
        $tags = json_decode($this->settings->get('huseyinfiliz.traderfeedback.feedbackActionTagFilter', '[]'), true);
        $tags = is_array($tags) ? array_map('strval', $tags) : [];

        if (!$discussionId) {
            if ($onlyLocked || $tags || (bool) $this->settings->get('huseyinfiliz.traderfeedback.requireDiscussion', false)) {
                $this->fail('discussion_id', 'discussion_required');
            }

            return;
        }

        $discussion = Discussion::query()->whereVisibleTo($actor)->find($discussionId);
        if (!$discussion || $discussion->hidden_at !== null) {
            $this->fail('discussion_id', 'discussion_not_found');
        }

        if ($onlyLocked && !$discussion->getAttribute('is_locked')) {
            $this->fail('discussion_id', 'discussion_not_locked');
        }

        if ($tags && (!$discussion->isRelation('tags') || !array_intersect($tags, $discussion->getRelationValue('tags')->modelKeys()))) {
            $this->fail('discussion_id', 'discussion_tag_not_allowed');
        }
    }

    public function assertCanCreate(User $actor, int $recipientId, ?int $discussionId): void
    {
        if ($actor->id === $recipientId) {
            $this->fail('to_user_id', 'self_feedback');
        }

        if (Feedback::withTrashed()->where('from_user_id', $actor->id)->where('created_at', '>', Carbon::now()->subMinute())->exists()) {
            $this->fail('rate_limit', 'rate_limit_feedback', ['seconds' => 60]);
        }

        if ($discussionId && (bool) $this->settings->get('huseyinfiliz.traderfeedback.onePerDiscussion', false)
            && Feedback::where('from_user_id', $actor->id)->where('to_user_id', $recipientId)->where('discussion_id', $discussionId)->exists()) {
            $this->fail('discussion_id', 'already_given');
        }
    }

    private function fail(string $field, string $key, array $parameters = []): never
    {
        throw new ValidationException([
            $field => $this->translator->trans('huseyinfiliz-traderfeedback.api.validation.'.$key, $parameters),
        ]);
    }
}
