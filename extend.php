<?php

namespace HuseyinFiliz\TraderFeedback;

use Flarum\Api\Context;
use Flarum\Api\Endpoint;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Access\FeedbackPolicy;
use HuseyinFiliz\TraderFeedback\Access\GlobalPolicy;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ApproveFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ApproveReportController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\CreateFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\DeleteFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\DismissReportController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListDiscussionParticipantsController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListFeedbacksController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListPendingFeedbacksController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListReportsController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\RejectFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\RejectReportController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ReportFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ShowFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ShowTraderStatsController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\StatsSummaryController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\UpdateFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Resource\FeedbackReportResource;
use HuseyinFiliz\TraderFeedback\Api\Resource\FeedbackResource;
use HuseyinFiliz\TraderFeedback\Api\Resource\TraderStatsResource;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use HuseyinFiliz\TraderFeedback\Listeners\AddUserPreferencesListener;
use HuseyinFiliz\TraderFeedback\Listeners\FeedbackCreatedListener;
use HuseyinFiliz\TraderFeedback\Listeners\FeedbackUpdatedListener;
use HuseyinFiliz\TraderFeedback\Listeners\UserDeletedListener;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackReportedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;

return [
    // Register assets and routes for the forum frontend
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less')
        ->route('/u/{username}/feedbacks', 'user.feedbacks'),

    // Register assets for the admin frontend
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    // Register locales
    new Extend\Locales(__DIR__.'/resources/locale'),

    // API routes
    (new Extend\Routes('api'))
        ->get('/trader/feedback', 'trader.feedback.index', ListFeedbacksController::class)
        ->post('/trader/feedback', 'trader.feedback.create', CreateFeedbackController::class)
        ->get('/trader/feedback/pending', 'trader.feedback.pending', ListPendingFeedbacksController::class)
        ->get('/trader/feedback/{id}', 'trader.feedback.show', ShowFeedbackController::class)
        ->patch('/trader/feedback/{id}', 'trader.feedback.update', UpdateFeedbackController::class)
        ->delete('/trader/feedback/{id}', 'trader.feedback.delete', DeleteFeedbackController::class)
        ->post('/trader/feedback/{id}/report', 'trader.feedback.report', ReportFeedbackController::class)
        ->post('/trader/feedback/{id}/approve', 'trader.feedback.approve', ApproveFeedbackController::class)
        ->post('/trader/feedback/{id}/reject', 'trader.feedback.reject', RejectFeedbackController::class)
        ->get('/trader/reports', 'trader.reports.index', ListReportsController::class)
        ->post('/trader/reports/{id}/approve', 'trader.reports.approve', ApproveReportController::class)
        ->post('/trader/reports/{id}/reject', 'trader.reports.reject', RejectReportController::class)
        ->post('/trader/reports/{id}/dismiss', 'trader.reports.dismiss', DismissReportController::class)
        ->get('/trader/stats/summary', 'trader.stats.summary', StatsSummaryController::class)
        ->get('/trader/stats/{id}', 'trader.stats.show', ShowTraderStatsController::class)
        ->get('/trader/discussions/{id}/participants', 'trader.discussions.participants', ListDiscussionParticipantsController::class),

    // Extend the User model with relationships
    (new Extend\Model(User::class))
        ->hasMany('feedbacksReceived', Feedback::class, 'to_user_id')
        ->hasMany('feedbacksGiven', Feedback::class, 'from_user_id')
        ->hasOne('traderStats', TraderStats::class, 'user_id'),

    // Extend the Feedback model with relationships
    (new Extend\Model(Feedback::class))
        ->belongsTo('fromUser', User::class, 'from_user_id')
        ->belongsTo('toUser', User::class, 'to_user_id')
        ->belongsTo('approvedBy', User::class, 'approved_by_id')
        ->belongsTo('discussion', Discussion::class, 'discussion_id'),

    // Register official API Resources
    new Extend\ApiResource(FeedbackResource::class),
    new Extend\ApiResource(TraderStatsResource::class),
    new Extend\ApiResource(FeedbackReportResource::class),

    // Extend UserResource in Flarum 2
    (new Extend\ApiResource(Resource\UserResource::class))
        ->fields(fn () => [
            Schema\Relationship\ToOne::make('traderStats')
                ->type('trader-stats')
                ->includable(),
            Schema\Relationship\ToMany::make('feedbacksReceived')
                ->type('trader-feedbacks')
                ->includable(),
            Schema\Relationship\ToMany::make('feedbacksGiven')
                ->type('trader-feedbacks')
                ->includable(),
            Schema\Boolean::make('canGiveFeedback')
                ->get(fn (User $user, Context $context) => (bool) (
                    $context->getActor()->id &&
                    $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.give') &&
                    $context->getActor()->id !== $user->id
                )),
            Schema\Boolean::make('canReportFeedback')
                ->get(fn (User $user, Context $context) => (bool) $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.report')),
            Schema\Boolean::make('canDeleteFeedback')
                ->get(fn (User $user, Context $context) => (bool) $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.delete')),
            Schema\Boolean::make('canModerateFeedback')
                ->get(fn (User $user, Context $context) => (bool) $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.moderate')),
            Schema\Integer::make('traderFeedbackCount')
                ->get(function (User $user) {
                    if ($user->relationLoaded('traderStats') && $user->traderStats) {
                        return (int) ($user->traderStats->positive_count + $user->traderStats->neutral_count + $user->traderStats->negative_count);
                    }
                    if ($user->traderStats) {
                        return (int) ($user->traderStats->positive_count + $user->traderStats->neutral_count + $user->traderStats->negative_count);
                    }
                    return (int) Feedback::where('to_user_id', $user->id)->where('is_approved', true)->count();
                }),
            Schema\Integer::make('pendingFeedbackCount')
                ->get(fn (User $user, Context $context) => $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.moderate')
                    ? Feedback::where('to_user_id', $user->id)->where('is_approved', false)->count()
                    : 0
                ),
            Schema\Integer::make('pendingReportCount')
                ->get(fn (User $user, Context $context) => $context->getActor()->hasPermission('huseyinfiliz-traderfeedback.moderate')
                    ? FeedbackReport::where('resolved', false)->whereHas('feedback', fn ($q) => $q->where('to_user_id', $user->id))->count()
                    : 0
                ),
        ])
        ->endpoint(Endpoint\Show::class, fn (Endpoint\Show $endpoint) => $endpoint->eagerLoad(['traderStats']))
        ->endpoint(Endpoint\Index::class, fn (Endpoint\Index $endpoint) => $endpoint->eagerLoad(['traderStats'])),

    (new Extend\ApiResource(Resource\DiscussionResource::class))
        ->endpoint(Endpoint\Show::class, fn (Endpoint\Show $endpoint) => $endpoint->eagerLoad(['posts.user.traderStats'])),

    (new Extend\ApiResource(Resource\PostResource::class))
        ->endpoint(Endpoint\Index::class, fn (Endpoint\Index $endpoint) => $endpoint->eagerLoad(['user.traderStats']))
        ->endpoint(Endpoint\Show::class, fn (Endpoint\Show $endpoint) => $endpoint->eagerLoad(['user.traderStats'])),

    // Register notification preferences
    (new Extend\User())
        ->registerPreference('notify_newFeedback_alert', 'boolval', true)
        ->registerPreference('notify_feedbackApproved_alert', 'boolval', true)
        ->registerPreference('notify_feedbackRejected_alert', 'boolval', true)
        ->registerPreference('notify_feedbackNeedsApproval_alert', 'boolval', true)
        ->registerPreference('notify_feedbackReported_alert', 'boolval', true),

    // Permissions with defaults
    (new Extend\Policy())
        ->globalPolicy(GlobalPolicy::class)
        ->modelPolicy(Feedback::class, FeedbackPolicy::class),

    // Notification type registration
    (new Extend\Notification())
        ->type(NewFeedbackBlueprint::class, ['alert'])
        ->type(FeedbackApprovedBlueprint::class, ['alert'])
        ->type(FeedbackRejectedBlueprint::class, ['alert'])
        ->type(FeedbackNeedsApprovalBlueprint::class, ['alert'])
        ->type(FeedbackReportedBlueprint::class, ['alert']),

    // Flarum Audit integration
    (new Extend\Conditional())
        ->whenExtensionEnabled('flarum-audit', fn () => [
            (new \Flarum\Audit\Extend\Audit())
                ->listen(
                    Events\FeedbackApproved::class,
                    'traderfeedback.approved',
                    fn (Events\FeedbackApproved $event) => [
                        'feedback_id' => $event->feedback->id,
                        'author'      => $event->feedback->fromUser?->username ?? 'Unknown',
                        'recipient'   => $event->feedback->toUser?->username ?? 'Unknown',
                        'user_id'     => $event->feedback->to_user_id,
                    ]
                )
                ->listen(
                    Events\FeedbackRejected::class,
                    'traderfeedback.rejected',
                    fn (Events\FeedbackRejected $event) => [
                        'feedback_id' => $event->feedback->id,
                        'author'      => $event->feedback->fromUser?->username ?? 'Unknown',
                        'recipient'   => $event->feedback->toUser?->username ?? 'Unknown',
                        'user_id'     => $event->feedback->to_user_id,
                    ]
                )
                ->listen(
                    Events\FeedbackDeleted::class,
                    'traderfeedback.deleted',
                    fn (Events\FeedbackDeleted $event) => [
                        'feedback_id' => $event->feedback->id,
                        'author'      => $event->feedback->fromUser?->username ?? 'Unknown',
                        'recipient'   => $event->feedback->toUser?->username ?? 'Unknown',
                        'user_id'     => $event->feedback->to_user_id,
                    ]
                )
                ->listen(
                    Events\ReportDismissed::class,
                    'traderfeedback.report_dismissed',
                    fn (Events\ReportDismissed $event) => [
                        'report_id'   => $event->report->id,
                        'feedback_id' => $event->report->feedback_id,
                        'recipient'   => $event->report->feedback?->toUser?->username ?? 'Unknown',
                        'user_id'     => $event->report->feedback?->to_user_id,
                    ]
                ),
        ]),

    // Event listeners
    (new Extend\Event())
        ->listen(\Flarum\User\Event\Saving::class, AddUserPreferencesListener::class)
        ->listen(\Flarum\User\Event\Deleted::class, UserDeletedListener::class)
        ->listen(FeedbackCreated::class, FeedbackCreatedListener::class)
        ->listen(FeedbackUpdated::class, FeedbackUpdatedListener::class),

    // Settings defaults and forum serialization
    (new Extend\Settings())
        ->default('huseyinfiliz.traderfeedback.requireApproval', false)
        ->default('huseyinfiliz.traderfeedback.allowNegative', true)
        ->default('huseyinfiliz.traderfeedback.requireDiscussion', false)
        ->default('huseyinfiliz.traderfeedback.onePerDiscussion', false)
        ->default('huseyinfiliz.traderfeedback.minLength', 10)
        ->default('huseyinfiliz.traderfeedback.maxLength', 1000)
        ->default('huseyinfiliz.traderfeedback.minDays', 0)
        ->default('huseyinfiliz.traderfeedback.minPosts', 0)

        // Post/Discussion feedback action defaults
        ->default('huseyinfiliz.traderfeedback.showFeedbackInPostMenu', false)
        ->default('huseyinfiliz.traderfeedback.showFeedbackBelowReply', false)
        ->default('huseyinfiliz.traderfeedback.showFeedbackInPostFooter', false)
        ->default('huseyinfiliz.traderfeedback.footerOnlyFirstPost', false)
        ->default('huseyinfiliz.traderfeedback.feedbackActionTagFilter', '[]')
        ->default('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked', false)

        ->default('huseyinfiliz.traderfeedback.showBadgeInPosts', true)

        // Badge display settings
        ->default('huseyinfiliz.traderfeedback.badgeCustomPrefix', '')
        ->default('huseyinfiliz.traderfeedback.badgeFormat', 'percentage')
        ->default('huseyinfiliz.traderfeedback.badgeCustomFormat', '{total} ({score}%) - {positive}P / {neutral}N / {negative}N')
        ->default('huseyinfiliz.traderfeedback.badgeTagFilter', '[]')
        ->default('huseyinfiliz.traderfeedback.badgeOnlyFirstPost', false)

        // Serialize to forum
        ->serializeToForum('huseyinfiliz.traderfeedback.requireApproval', 'huseyinfiliz.traderfeedback.requireApproval', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.allowNegative', 'huseyinfiliz.traderfeedback.allowNegative', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.requireDiscussion', 'huseyinfiliz.traderfeedback.requireDiscussion', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.onePerDiscussion', 'huseyinfiliz.traderfeedback.onePerDiscussion', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.minLength', 'huseyinfiliz.traderfeedback.minLength', 'intval')
        ->serializeToForum('huseyinfiliz.traderfeedback.maxLength', 'huseyinfiliz.traderfeedback.maxLength', 'intval')

        // Post/Discussion feedback action serialization
        ->serializeToForum('huseyinfiliz.traderfeedback.showFeedbackInPostMenu', 'huseyinfiliz.traderfeedback.showFeedbackInPostMenu', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.showFeedbackBelowReply', 'huseyinfiliz.traderfeedback.showFeedbackBelowReply', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.showFeedbackInPostFooter', 'huseyinfiliz.traderfeedback.showFeedbackInPostFooter', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.footerOnlyFirstPost', 'huseyinfiliz.traderfeedback.footerOnlyFirstPost', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.feedbackActionTagFilter', 'huseyinfiliz.traderfeedback.feedbackActionTagFilter')
        ->serializeToForum('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked', 'huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked', 'boolval')

        ->serializeToForum('huseyinfiliz.traderfeedback.showBadgeInPosts', 'huseyinfiliz.traderfeedback.showBadgeInPosts', 'boolval')

        // Badge display serialization
        ->serializeToForum('huseyinfiliz.traderfeedback.badgeCustomPrefix', 'huseyinfiliz.traderfeedback.badgeCustomPrefix')
        ->serializeToForum('huseyinfiliz.traderfeedback.badgeFormat', 'huseyinfiliz.traderfeedback.badgeFormat')
        ->serializeToForum('huseyinfiliz.traderfeedback.badgeCustomFormat', 'huseyinfiliz.traderfeedback.badgeCustomFormat')
        ->serializeToForum('huseyinfiliz.traderfeedback.badgeTagFilter', 'huseyinfiliz.traderfeedback.badgeTagFilter')
        ->serializeToForum('huseyinfiliz.traderfeedback.badgeOnlyFirstPost', 'huseyinfiliz.traderfeedback.badgeOnlyFirstPost', 'boolval'),
];
