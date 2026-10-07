<?php

namespace HuseyinFiliz\TraderFeedback\Api\Resource;

use Flarum\Api\Endpoint;
use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;

/**
 * @extends AbstractDatabaseResource<TraderStats>
 */
class TraderStatsResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'trader-stats';
    }

    public function model(): string
    {
        return TraderStats::class;
    }

    public function endpoints(): array
    {
        return [
            Endpoint\Index::make()
                ->paginate(),
            Endpoint\Show::make(),
        ];
    }

    public function fields(): array
    {
        return [
            Schema\Integer::make('positiveCount')
                ->property('positive_count'),
            Schema\Integer::make('negativeCount')
                ->property('negative_count'),
            Schema\Integer::make('neutralCount')
                ->property('neutral_count'),
            Schema\Number::make('score'),
            Schema\DateTime::make('lastUpdated')
                ->property('last_updated'),
            Schema\Relationship\ToOne::make('user')
                ->type('users')
                ->includable(),
        ];
    }
}
