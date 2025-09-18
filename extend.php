<?php

use Flarum\Extend;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\User;
use Flarum\Group\Group;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListFeedbacksController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\CreateFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListPendingFeedbacksController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ShowFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\UpdateFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\DeleteFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ReportFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ApproveFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\RejectFeedbackController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ListReportsController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ApproveReportController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\RejectReportController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\DismissReportController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\ShowTraderStatsController;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Api\Serializers\TraderStatsSerializer;
use HuseyinFiliz\TraderFeedback\Listeners\AddUserPreferencesListener;
use HuseyinFiliz\TraderFeedback\Listeners\UserDeletedListener;
use HuseyinFiliz\TraderFeedback\Listeners\FeedbackCreatedListener;
use HuseyinFiliz\TraderFeedback\Listeners\FeedbackUpdatedListener;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use HuseyinFiliz\TraderFeedback\Notifications\NewFeedbackBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackApprovedBlueprint;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackRejectedBlueprint;
use HuseyinFiliz\TraderFeedback\Access\FeedbackPolicy;
use HuseyinFiliz\TraderFeedback\Access\GlobalPolicy;
use HuseyinFiliz\TraderFeedback\Events\FeedbackCreated;
use HuseyinFiliz\TraderFeedback\Events\FeedbackUpdated;
use Flarum\Notification\Notification;
use Flarum\Api\Serializer\NotificationSerializer;
use Flarum\Notification\Event\Sending;

return [
    // Register assets and routes for the forum frontend
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/resources/less/forum.less')
        ->route('/u/{username}/feedbacks', 'user.feedbacks'),

    // Register assets for the admin frontend
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/resources/less/admin.less'),

    // Register locales
    new Extend\Locales(__DIR__ . '/resources/locale'),

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
        ->get('/trader/stats/{id}', 'trader.stats.show', ShowTraderStatsController::class),

    // Extend the User model with relationships
    (new Extend\Model(User::class))
        ->hasMany('feedbacksReceived', Feedback::class, 'to_user_id')
        ->hasMany('feedbacksGiven', Feedback::class, 'from_user_id')
        ->hasOne('traderStats', TraderStats::class, 'user_id'),

    // Extend the UserSerializer to add permission attributes
    (new Extend\ApiSerializer(UserSerializer::class))
        ->hasMany('feedbacksReceived', FeedbackSerializer::class)
        ->hasMany('feedbacksGiven', FeedbackSerializer::class)
        ->hasOne('traderStats', TraderStatsSerializer::class)
        ->attributes(function (UserSerializer $serializer, User $user, array $attributes) {
            $actor = $serializer->getActor();

            $attributes['canGiveFeedback'] = $actor &&
                $actor->hasPermission('huseyinfiliz-traderfeedback.give') &&
                $actor->id !== $user->id;

            $attributes['canReportFeedback'] = $actor &&
                $actor->hasPermission('huseyinfiliz-traderfeedback.report');

            $attributes['canDeleteFeedback'] = $actor &&
                $actor->hasPermission('huseyinfiliz-traderfeedback.delete');

            $attributes['canModerateFeedback'] = $actor &&
                $actor->hasPermission('huseyinfiliz-traderfeedback.moderate');

            return $attributes;
        }),

    // Add the notification data to the serializer. This mirrors Flarum's
    // behaviour of exposing the `data` payload in a `content` attribute so
    // that the frontend can call `notification.content()`.
    (new Extend\ApiSerializer(NotificationSerializer::class))
        ->attributes(function (NotificationSerializer $serializer, Notification $notification) {
            $attributes = [];

            if (in_array($notification->type, [
                NewFeedbackBlueprint::getType(),
                FeedbackApprovedBlueprint::getType(),
                FeedbackRejectedBlueprint::getType(),
            ])) {
                $attributes['content'] = $notification->data;
            }

            return $attributes;
        }),

    // Register notification preferences. Use Flarum's helper to generate
    // correctly formatted keys (e.g. notify_newFeedback_alert) and set them
    // to `true` by default so that alerts are enabled out of the box.
    (new Extend\User())
        ->registerPreference(
            User::getNotificationPreferenceKey(NewFeedbackBlueprint::getType(), 'alert'),
            'boolval',
            true
        )
        ->registerPreference(
            User::getNotificationPreferenceKey(FeedbackApprovedBlueprint::getType(), 'alert'),
            'boolval',
            true
        )
        ->registerPreference(
            User::getNotificationPreferenceKey(FeedbackRejectedBlueprint::getType(), 'alert'),
            'boolval',
            true
        ),

    // Permissions with defaults
    (new Extend\Policy())
        ->globalPolicy(GlobalPolicy::class)
        ->modelPolicy(Feedback::class, FeedbackPolicy::class),

    // Register our notification types. Only the `alert` channel is enabled.
    (new Extend\Notification())
        ->type(NewFeedbackBlueprint::class, BasicUserSerializer::class, ['alert'])
        ->type(FeedbackApprovedBlueprint::class, BasicUserSerializer::class, ['alert'])
        ->type(FeedbackRejectedBlueprint::class, BasicUserSerializer::class, ['alert']),

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
        ->default('huseyinfiliz.traderfeedback.onePerDiscussion', true)
        ->default('huseyinfiliz.traderfeedback.minLength', 10)
        ->default('huseyinfiliz.traderfeedback.maxLength', 1000)
        ->default('huseyinfiliz.traderfeedback.minDays', 0)
        ->default('huseyinfiliz.traderfeedback.minPosts', 0)
        ->serializeToForum('huseyinfiliz.traderfeedback.requireApproval', 'huseyinfiliz.traderfeedback.requireApproval', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.allowNegative', 'huseyinfiliz.traderfeedback.allowNegative', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.requireDiscussion', 'huseyinfiliz.traderfeedback.requireDiscussion', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.onePerDiscussion', 'huseyinfiliz.traderfeedback.onePerDiscussion', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.minLength', 'huseyinfiliz.traderfeedback.minLength', 'intval')
        ->serializeToForum('huseyinfiliz.traderfeedback.maxLength', 'huseyinfiliz.traderfeedback.maxLength', 'intval'),
];