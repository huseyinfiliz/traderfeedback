<?php

namespace HuseyinFiliz\TraderFeedback\Api\Resource;

use Flarum\Api\Endpoint;
use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use Illuminate\Database\Eloquent\Builder;
use Tobyz\JsonApiServer\Context as OriginalContext;

/**
 * @extends AbstractDatabaseResource<FeedbackReport>
 */
class FeedbackReportResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'feedback-reports';
    }

    public function model(): string
    {
        return FeedbackReport::class;
    }

    public function scope(Builder $query, OriginalContext $context): void
    {
        $actor = $context->getActor();

        if (!$actor->hasPermission('huseyinfiliz-traderfeedback.moderate')) {
            $query->whereRaw('0 = 1');
        }
    }

    public function endpoints(): array
    {
        return [
            Endpoint\Index::make()
                ->paginate()
                ->defaultInclude(['reporter', 'feedback', 'feedback.fromUser', 'feedback.toUser'])
                ->eagerLoad(['reporter', 'feedback', 'feedback.fromUser', 'feedback.toUser']),
            Endpoint\Show::make(),
            Endpoint\Create::make()
                ->authenticated(),
            Endpoint\Delete::make()
                ->authenticated()
                ->can('huseyinfiliz-traderfeedback.moderate'),
        ];
    }

    public function fields(): array
    {
        return [
            Schema\Str::make('reason')
                ->requiredOnCreate()
                ->writable(),
            Schema\Boolean::make('resolved')
                ->writable(),
            Schema\DateTime::make('createdAt')
                ->property('created_at'),
            Schema\DateTime::make('updatedAt')
                ->property('updated_at'),
            Schema\Relationship\ToOne::make('reporter')
                ->type('users')
                ->includable(),
            Schema\Relationship\ToOne::make('user')
                ->type('users')
                ->property('reporter')
                ->includable(),
            Schema\Relationship\ToOne::make('feedback')
                ->type('trader-feedbacks')
                ->includable(),
            Schema\Relationship\ToOne::make('resolvedBy')
                ->type('users')
                ->includable(),
        ];
    }
}
