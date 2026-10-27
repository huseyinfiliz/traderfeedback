<?php

namespace HuseyinFiliz\TraderFeedback\Api\Resource;

use Flarum\Api\Context;
use Flarum\Api\Endpoint;
use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use Flarum\Api\Sort\SortColumn;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Illuminate\Database\Eloquent\Builder;
use Tobyz\JsonApiServer\Context as OriginalContext;

/**
 * @extends AbstractDatabaseResource<Feedback>
 */
class FeedbackResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'trader-feedbacks';
    }

    public function model(): string
    {
        return Feedback::class;
    }

    public function scope(Builder $query, OriginalContext $context): void
    {
        $actor = $context->getActor();

        if (!$actor->hasPermission('huseyinfiliz-traderfeedback.moderate')) {
            $query->where('is_approved', true);
        }
    }

    public function endpoints(): array
    {
        return [
            Endpoint\Index::make()
                ->paginate()
                ->defaultInclude(['fromUser', 'toUser', 'discussion'])
                ->eagerLoad(['fromUser', 'toUser', 'discussion']),
            Endpoint\Show::make()
                ->defaultInclude(['fromUser', 'toUser', 'discussion'])
                ->eagerLoad(['fromUser', 'toUser', 'discussion']),
        ];
    }

    public function fields(): array
    {
        return [
            Schema\Str::make('type'),
            Schema\Str::make('role'),
            Schema\Str::make('comment'),
            Schema\Boolean::make('isApproved')
                ->property('is_approved'),
            Schema\DateTime::make('createdAt')
                ->property('created_at'),
            Schema\DateTime::make('updatedAt')
                ->property('updated_at'),
            Schema\Integer::make('fromUserId')
                ->property('from_user_id'),
            Schema\Integer::make('toUserId')
                ->property('to_user_id'),
            Schema\Integer::make('discussionId')
                ->get(fn (Feedback $feedback) => $feedback->relationLoaded('discussion') && $feedback->discussion ? $feedback->discussion_id : null)
                ->nullable(),
            Schema\Boolean::make('canEdit')
                ->get(fn (Feedback $feedback, Context $context) => $context->getActor()->can('edit', $feedback)),
            Schema\Boolean::make('canDelete')
                ->get(fn (Feedback $feedback, Context $context) => $context->getActor()->can('delete', $feedback)),
            Schema\Boolean::make('canReport')
                ->get(fn (Feedback $feedback, Context $context) => $context->getActor()->can('report', $feedback)),
            Schema\Boolean::make('canModerate')
                ->get(fn (Feedback $feedback, Context $context) => $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.moderate')),
            Schema\Relationship\ToOne::make('fromUser')
                ->type('users')
                ->includable(),
            Schema\Relationship\ToOne::make('toUser')
                ->type('users')
                ->includable(),
            Schema\Relationship\ToOne::make('discussion')
                ->type('discussions')
                ->includable(),
            Schema\Relationship\ToOne::make('approvedBy')
                ->type('users')
                ->includable(),
        ];
    }

    public function sorts(): array
    {
        return [
            SortColumn::make('createdAt')
                ->column('created_at'),
        ];
    }
}
