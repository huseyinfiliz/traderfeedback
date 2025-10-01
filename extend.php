<?php

use Flarum\Extend;
use Flarum\Api\Controller\ShowUserController;
use Flarum\Api\Controller\ListUsersController;
use Flarum\Api\Controller\ShowDiscussionController;  // ✅ YENİ
use Flarum\Api\Controller\ListPostsController;      // ✅ YENİ
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
use HuseyinFiliz\TraderFeedback\Api\Controllers\TestNotificationController;
use HuseyinFiliz\TraderFeedback\Api\Controllers\StatsSummaryController;
use HuseyinFiliz\TraderFeedback\Api\Serializers\StatsSummarySerializer;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Api\Serializers\TraderStatsSerializer;
use HuseyinFiliz\TraderFeedback\Api\Serializers\FeedbackReportSerializer;
use HuseyinFiliz\TraderFeedback\Listeners\AddUserPreferencesListener;
use HuseyinFiliz\TraderFeedback\Listeners\UserDeletedListener;
use HuseyinFiliz\TraderFeedback\Listeners\FeedbackCreatedListener;
use HuseyinFiliz\TraderFeedback\Listeners\FeedbackUpdatedListener;
use HuseyinFiliz\TraderFeedback\Listeners\NotificationSendingListener;
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
        ->get('/trader/stats/summary', 'trader.stats.summary', StatsSummaryController::class)
        ->get('/trader/stats/{id}', 'trader.stats.show', ShowTraderStatsController::class)
        ->get('/trader/test-notification', 'trader.test.notification', TestNotificationController::class),

    // Extend the User model with relationships
    (new Extend\Model(User::class))
        ->hasMany('feedbacksReceived', Feedback::class, 'to_user_id')
        ->hasMany('feedbacksGiven', Feedback::class, 'from_user_id')
        ->hasOne('traderStats', TraderStats::class, 'user_id'),

    // Extend the Feedback model with relationships
    (new Extend\Model(Feedback::class))
        ->belongsTo('fromUser', User::class, 'from_user_id')
        ->belongsTo('toUser', User::class, 'to_user_id')
        ->belongsTo('approvedBy', User::class, 'approved_by_id'),

    // NOT: Notification model'e relationship EKLEME!
    // Flarum otomatik olarak Blueprint'teki getSubject() ve getSubjectModel() 
    // kullanarak subject'i yükler. Manuel relationship gereksiz ve zararlı!

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
    
    // ✅ API Controller includes - Profil ve kullanıcı listeleri için
    (new Extend\ApiController(ShowUserController::class))
        ->addInclude('traderStats'),

    (new Extend\ApiController(ListUsersController::class))
        ->addInclude('traderStats'),
    
    // ✅ YENİ - Tartışma sayfalarındaki post user cards için
    (new Extend\ApiController(ShowDiscussionController::class))
        ->addInclude('posts.user.traderStats')
        ->load(['posts.user.traderStats']),  // N+1 sorguları önle
    
    // ✅ YENİ - Post listelerindeki user cards için
    (new Extend\ApiController(ListPostsController::class))
        ->addInclude('user.traderStats')
        ->load(['user.traderStats']),  // N+1 sorguları önle
    
    // NOT: hasOne('subject') EKLEME! 
    // Flarum otomatik serialize eder, manuel ekleme zararlı!

    // Register notification preferences
    (new Extend\User())
        ->registerPreference('notify_newFeedback_alert', 'boolval', true)
        ->registerPreference('notify_feedbackApproved_alert', 'boolval', true)
        ->registerPreference('notify_feedbackRejected_alert', 'boolval', true),

    // Permissions with defaults
    (new Extend\Policy())
        ->globalPolicy(GlobalPolicy::class)
        ->modelPolicy(Feedback::class, FeedbackPolicy::class),

    // ✅ Notification type registration - FeedbackSerializer subject için kullanılır
    (new Extend\Notification())
        ->type(NewFeedbackBlueprint::class, FeedbackSerializer::class, ['alert'])
        ->type(FeedbackApprovedBlueprint::class, FeedbackSerializer::class, ['alert'])
        ->type(FeedbackRejectedBlueprint::class, FeedbackSerializer::class, ['alert']),

    // Event listeners
    (new Extend\Event())
        ->listen(\Flarum\User\Event\Saving::class, AddUserPreferencesListener::class)
        ->listen(\Flarum\User\Event\Deleted::class, UserDeletedListener::class)
        ->listen(FeedbackCreated::class, FeedbackCreatedListener::class)
        ->listen(FeedbackUpdated::class, FeedbackUpdatedListener::class)
        ->listen(Sending::class, NotificationSendingListener::class),

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
        ->default('huseyinfiliz.traderfeedback.showBadgeInPosts', true)
        ->serializeToForum('huseyinfiliz.traderfeedback.requireApproval', 'huseyinfiliz.traderfeedback.requireApproval', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.allowNegative', 'huseyinfiliz.traderfeedback.allowNegative', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.requireDiscussion', 'huseyinfiliz.traderfeedback.requireDiscussion', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.onePerDiscussion', 'huseyinfiliz.traderfeedback.onePerDiscussion', 'boolval')
        ->serializeToForum('huseyinfiliz.traderfeedback.minLength', 'huseyinfiliz.traderfeedback.minLength', 'intval')
        ->serializeToForum('huseyinfiliz.traderfeedback.maxLength', 'huseyinfiliz.traderfeedback.maxLength', 'intval')
        ->serializeToForum('huseyinfiliz.traderfeedback.showBadgeInPosts', 'huseyinfiliz.traderfeedback.showBadgeInPosts', 'boolval'),  // ✅ YENİ ayar için serialize
];