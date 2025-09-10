<?php

use Flarum\Extend;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
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

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/resources/less/forum.less')
        ->route('/u/{username}/feedbacks', 'user.feedbacks'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/resources/less/admin.less'),

    new Extend\Locales(__DIR__ . '/resources/locale'),

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

    (new Extend\Model(User::class))
        ->hasMany('feedbacksReceived', Feedback::class, 'to_user_id')
        ->hasMany('feedbacksGiven', Feedback::class, 'from_user_id')
        ->hasOne('traderStats', TraderStats::class, 'user_id'),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->hasMany('feedbacksReceived', FeedbackSerializer::class)
        ->hasMany('feedbacksGiven', FeedbackSerializer::class)
        ->hasOne('traderStats', TraderStatsSerializer::class)
        ->attributes(function (UserSerializer $serializer, User $user, array $attributes) {
            $actor = $serializer->getActor();
            
            $canGive = $actor && $actor->can('huseyinfiliz-traderfeedback.giveFeedback');
            $isDifferentUser = $actor && $actor->id !== $user->id;
            
            $attributes['canGiveFeedback'] = $canGive && $isDifferentUser;
            $attributes['canModerateFeedback'] = $actor && $actor->can('huseyinfiliz-traderfeedback.moderateFeedback');
            
            return $attributes;
        }),

    // Permissions
    (new Extend\Policy())
        ->globalPolicy(GlobalPolicy::class)
        ->modelPolicy(Feedback::class, FeedbackPolicy::class),

    (new Extend\Notification())
        ->type(NewFeedbackBlueprint::class, FeedbackSerializer::class, ['alert', 'email'])
        ->type(FeedbackApprovedBlueprint::class, FeedbackSerializer::class, ['alert', 'email'])
        ->type(FeedbackRejectedBlueprint::class, FeedbackSerializer::class, ['alert', 'email']),

    // Event listeners
    (new Extend\Event())
        ->listen(\Flarum\User\Event\Saving::class, AddUserPreferencesListener::class)
        ->listen(\Flarum\User\Event\Deleted::class, UserDeletedListener::class)
        ->listen(FeedbackCreated::class, FeedbackCreatedListener::class)
        ->listen(FeedbackUpdated::class, FeedbackUpdatedListener::class),
];