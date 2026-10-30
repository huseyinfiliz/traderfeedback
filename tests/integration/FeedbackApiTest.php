<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Integration;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\Notification;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use HuseyinFiliz\TraderFeedback\Notifications\FeedbackNeedsApprovalBlueprint;
use HuseyinFiliz\TraderFeedback\Services\ModeratorNotificationSyncer;
use HuseyinFiliz\TraderFeedback\Services\StatsService;
use Illuminate\Database\Eloquent\Builder;

class FeedbackApiTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('huseyinfiliz-traderfeedback');

        $this->prepareDatabase([
            User::class => [
                array_merge($this->normalUser(), [
                    'joined_at'     => Carbon::now()->subDays(10)->toDateTimeString(),
                    'comment_count' => 10,
                ]), // id 2: normal
                [
                    'id'                 => 3,
                    'username'           => 'recipient',
                    'password'           => '$2y$10$LO59tiT7uggl6Oe23o/O6.utnF6ipngYjvMvaxo1TciKqBttDNKim',
                    'email'              => 'recipient@machine.local',
                    'is_email_confirmed' => 1,
                    'joined_at'          => Carbon::now()->subDays(10)->toDateTimeString(),
                    'comment_count'      => 10,
                ],
                [
                    'id'                 => 4,
                    'username'           => 'unrelated',
                    'password'           => '$2y$10$LO59tiT7uggl6Oe23o/O6.utnF6ipngYjvMvaxo1TciKqBttDNKim',
                    'email'              => 'unrelated@machine.local',
                    'is_email_confirmed' => 1,
                    'joined_at'          => Carbon::now()->subDays(10)->toDateTimeString(),
                    'comment_count'      => 10,
                ],
            ],
            'group_user' => [
                ['user_id' => 1, 'group_id' => 1],
            ],
            'group_permission' => [
                ['group_id' => 3, 'permission' => 'huseyinfiliz-traderfeedback.give'],
                ['group_id' => 3, 'permission' => 'huseyinfiliz-traderfeedback.view'],
            ],
            Discussion::class => [
                [
                    'id'            => 1,
                    'title'         => 'Test Discussion',
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'user_id'       => 2,
                    'first_post_id' => 1,
                    'comment_count' => 1,
                ],
            ],
            Post::class => [
                [
                    'id'            => 1,
                    'discussion_id' => 1,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'user_id'       => 2,
                    'type'          => 'comment',
                    'content'       => '<t><p>Post content</p></t>',
                    'number'        => 1,
                ],
            ],
            'tfb_feedbacks' => [
                [
                    'id'            => 1,
                    'from_user_id'  => 2,
                    'to_user_id'    => 3,
                    'type'          => 'positive',
                    'role'          => 'buyer',
                    'comment'       => 'This is an approved feedback with enough characters for validation.',
                    'discussion_id' => 1,
                    'is_approved'   => 1,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                ],
                [
                    'id'            => 2,
                    'from_user_id'  => 2,
                    'to_user_id'    => 3,
                    'type'          => 'negative',
                    'role'          => 'seller',
                    'comment'       => 'This is a pending feedback with enough characters waiting for review.',
                    'discussion_id' => 1,
                    'is_approved'   => 0,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                ],
            ],
        ]);
    }

    public function test_guest_can_view_approved_feedback()
    {
        $response = $this->send($this->request('GET', '/api/trader/feedback/1'));
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('1', $body['data']['id']);
    }

    public function test_guest_cannot_view_pending_feedback()
    {
        $response = $this->send($this->request('GET', '/api/trader/feedback/2'));
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_unrelated_user_cannot_view_pending_feedback()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback/2', [
                'authenticatedAs' => 4,
            ])
        );
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_author_cannot_view_own_pending_feedback()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback/2', [
                'authenticatedAs' => 2,
            ])
        );
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_recipient_cannot_view_own_pending_feedback()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback/2', [
                'authenticatedAs' => 3,
            ])
        );
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_admin_can_view_pending_feedback()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback/2', [
                'authenticatedAs' => 1,
            ])
        );
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_list_feedbacks_only_returns_approved_feedbacks_for_guest()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback')
                ->withQueryParams(['filter' => ['user' => 3]])
        );
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $ids = array_column($body['data'], 'id');
        $this->assertContains('1', $ids);
        $this->assertNotContains('2', $ids);
    }

    public function test_guest_cannot_create_feedback()
    {
        $req = $this->request('POST', '/api/trader/feedback', [
            'json' => [
                'data' => [
                    'attributes' => [
                        'to_user_id' => 3,
                        'type'       => 'positive',
                        'role'       => 'buyer',
                        'comment'    => 'This is a test comment that meets length requirements.',
                    ],
                ],
            ],
        ])->withAttribute('bypassCsrfToken', true);

        $response = $this->send($req);
        $this->assertContains($response->getStatusCode(), [401, 403]);
    }

    public function test_user_cannot_give_feedback_to_self()
    {
        $req = $this->request('POST', '/api/trader/feedback', ['authenticatedAs' => 2]);
        $req = $this->requestWithJsonBody($req, [
            'data' => [
                'attributes' => [
                    'to_user_id' => 2,
                    'type'       => 'positive',
                    'role'       => 'buyer',
                    'comment'    => 'This is a test comment that meets length requirements.',
                ],
            ],
        ]);

        $response = $this->send($req);
        $this->assertSame(422, $response->getStatusCode());
    }

    public function test_unrelated_user_cannot_update_feedback()
    {
        $req = $this->request('PATCH', '/api/trader/feedback/1', ['authenticatedAs' => 4]);
        $req = $this->requestWithJsonBody($req, [
            'data' => [
                'attributes' => [
                    'comment' => 'Malicious update attempt by unrelated user.',
                ],
            ],
        ]);

        $response = $this->send($req);
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_author_can_update_feedback()
    {
        $req = $this->request('PATCH', '/api/trader/feedback/1', ['authenticatedAs' => 2]);
        $req = $this->requestWithJsonBody($req, [
            'data' => [
                'attributes' => [
                    'comment' => 'Updated comment by author that is long enough.',
                ],
            ],
        ]);

        $response = $this->send($req);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame('Updated comment by author that is long enough.', $body['data']['attributes']['comment']);
    }

    public function test_unrelated_user_cannot_delete_feedback()
    {
        $response = $this->send(
            $this->request('DELETE', '/api/trader/feedback/1', ['authenticatedAs' => 4])
        );
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_admin_can_delete_feedback()
    {
        $response = $this->send(
            $this->request('DELETE', '/api/trader/feedback/1', ['authenticatedAs' => 1])
        );
        $this->assertSame(204, $response->getStatusCode());
    }

    public function test_resource_mutations_are_unavailable_even_to_admin()
    {
        foreach ([['POST', '/api/trader-feedbacks'], ['PATCH', '/api/trader-feedbacks/1'], ['DELETE', '/api/trader-feedbacks/1'],
            ['POST', '/api/feedback-reports'], ['DELETE', '/api/feedback-reports/1']] as [$method, $path]) {
            $response = $this->mutate($method, $path, [], 1);
            $this->assertContains($response->getStatusCode(), [404, 405], $method.' '.$path);
        }
        $this->assertSame(2, Feedback::count());
    }

    public function test_create_validates_missing_required_fields()
    {
        $response = $this->mutate('POST', '/api/trader/feedback', ['comment' => 'A sufficiently long comment.']);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(2, Feedback::count());
    }

    public function test_create_sanitizes_and_records_feedback()
    {
        $response = $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['comment' => '<b>A successful transaction.</b>']));
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        $feedback = Feedback::latest('id')->first();
        $this->assertSame(4, $feedback->from_user_id);
        $this->assertSame('A successful transaction.', $feedback->comment);
        $stats = $this->app()->getContainer()->make(StatsService::class)->forUser(3);
        $this->assertSame(2, $stats->positive_count);
    }

    public function test_create_requires_approval_when_configured()
    {
        $this->setting('huseyinfiliz.traderfeedback.requireApproval', true);
        $response = $this->mutate('POST', '/api/trader/feedback', $this->validFeedback());
        $this->assertSame(201, $response->getStatusCode());
        $this->assertFalse(Feedback::latest('id')->first()->is_approved);
    }

    public function test_author_edit_returns_to_moderation_and_updates_stats()
    {
        $this->setting('huseyinfiliz.traderfeedback.requireApproval', true);
        $response = $this->mutate('PATCH', '/api/trader/feedback/1', ['type' => 'negative'], 2);
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $feedback = Feedback::findOrFail(1);
        $this->assertFalse($feedback->is_approved);
        $this->assertNull($feedback->approved_by_id);
        $stats = $this->app()->getContainer()->make(StatsService::class)->forUser(3);
        $this->assertSame(0, $stats->positive_count + $stats->negative_count);
        $this->assertSame(403, $this->send($this->request('GET', '/api/trader/feedback/1'))->getStatusCode());
        $response = $this->mutate('POST', '/api/trader/feedback/1/approve', [], 1);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1, $this->app()->getContainer()->make(StatsService::class)->forUser(3)->negative_count);
    }

    public function test_moderator_edit_and_no_op_edit_do_not_reset_approval()
    {
        $this->setting('huseyinfiliz.traderfeedback.requireApproval', true);
        $response = $this->mutate('PATCH', '/api/trader/feedback/1', ['comment' => 'A moderator corrected this feedback.'], 1);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(Feedback::findOrFail(1)->is_approved);
        $response = $this->mutate('PATCH', '/api/trader/feedback/1', ['comment' => 'A moderator corrected this feedback.'], 2);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(Feedback::findOrFail(1)->is_approved);
    }

    public function test_negative_feedback_setting_is_enforced_on_create_and_update()
    {
        $this->setting('huseyinfiliz.traderfeedback.allowNegative', false);
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['type' => 'negative']))->getStatusCode());
        $this->assertSame(422, $this->mutate('PATCH', '/api/trader/feedback/1', ['type' => 'negative'], 2)->getStatusCode());
        $this->assertSame('positive', Feedback::findOrFail(1)->type);
    }

    public function test_comment_limits_and_enum_values_are_enforced()
    {
        $this->setting('huseyinfiliz.traderfeedback.minLength', 15);
        $this->setting('huseyinfiliz.traderfeedback.maxLength', 40);
        foreach ([['comment' => 'short'], ['comment' => str_repeat('x', 41)], ['comment' => []], ['comment' => null],
            ['role' => 'invalid'], ['type' => 'invalid']] as $changes) {
            $this->assertSame(422, $this->mutate('PATCH', '/api/trader/feedback/1', $changes, 2)->getStatusCode());
        }
    }

    public function test_account_age_and_post_requirements_are_enforced()
    {
        $this->setting('huseyinfiliz.traderfeedback.minDays', 30);
        $this->setting('huseyinfiliz.traderfeedback.minPosts', 100);
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
        User::whereKey(4)->update(['joined_at' => Carbon::now()->subDays(40)]);
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
        User::whereKey(4)->update(['comment_count' => 100]);
        $this->assertSame(201, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
    }

    public function test_rate_limit_includes_recently_deleted_feedback()
    {
        $this->assertSame(201, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
        Feedback::where('from_user_id', 4)->delete();
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
    }

    public function test_deleting_an_author_recalculates_recipient_stats()
    {
        $stats = $this->app()->getContainer()->make(StatsService::class);
        $stats->updateUserStats(3);
        $this->assertSame(1, $stats->forUser(3)->positive_count);
        $response = $this->mutate('DELETE', '/api/users/2', [], 1);
        $this->assertSame(204, $response->getStatusCode(), (string) $response->getBody());
        $this->assertSame(0, $stats->forUser(3)->positive_count);
    }

    public function test_one_per_discussion_is_enforced_after_rate_limit_expires()
    {
        $this->setting('huseyinfiliz.traderfeedback.onePerDiscussion', true);
        $data = $this->validFeedback(['discussion_id' => 1]);
        $this->assertSame(201, $this->mutate('POST', '/api/trader/feedback', $data)->getStatusCode());
        Feedback::where('from_user_id', 4)->update(['created_at' => Carbon::now()->subMinutes(2)]);
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $data)->getStatusCode());
    }

    public function test_lock_restriction_requires_a_locked_discussion()
    {
        $this->setting('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked', true);
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['discussion_id' => 1]))->getStatusCode());
    }

    public function test_tag_restriction_cannot_be_bypassed_when_tags_extension_is_absent()
    {
        $this->setting('huseyinfiliz.traderfeedback.feedbackActionTagFilter', '["1"]');
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback())->getStatusCode());
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['discussion_id' => 1]))->getStatusCode());
    }

    public function test_lock_restriction_accepts_a_locked_discussion_with_lock_extension()
    {
        $this->extension('flarum-lock');
        $this->setting('huseyinfiliz.traderfeedback.feedbackOnlyWhenLocked', true);
        $this->app();
        Discussion::whereKey(1)->update(['is_locked' => true]);
        $response = $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['discussion_id' => 1]));
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());
    }

    public function test_tag_restriction_accepts_only_allowed_tags()
    {
        $this->extension('flarum-tags');
        $this->setting('huseyinfiliz.traderfeedback.feedbackActionTagFilter', '["1"]');
        $this->prepareDatabase([
            'tags' => [
                ['id' => 1, 'name' => 'Marketplace', 'slug' => 'marketplace'],
                ['id' => 2, 'name' => 'General', 'slug' => 'general'],
            ],
            'discussion_tag' => [['discussion_id' => 1, 'tag_id' => 2]],
        ]);
        $data = $this->validFeedback(['discussion_id' => 1]);
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $data)->getStatusCode());
        $this->database()->table('discussion_tag')->where('discussion_id', 1)->update(['tag_id' => 1]);
        $response = $this->mutate('POST', '/api/trader/feedback', $data);
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());
    }

    public function test_hidden_discussion_is_not_disclosed_or_accepted()
    {
        $this->app();
        Discussion::whereKey(1)->update(['hidden_at' => Carbon::now()]);
        $response = $this->send($this->request('GET', '/api/trader/feedback/1'));
        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNull($body['data']['attributes']['discussionId']);
        $this->assertEmpty(array_filter($body['included'], fn ($item) => $item['type'] === 'discussions'));
        $resource = $this->send($this->request('GET', '/api/trader-feedbacks/1'));
        $this->assertSame(200, $resource->getStatusCode());
        $resourceBody = json_decode((string) $resource->getBody(), true);
        $this->assertNull($resourceBody['data']['attributes']['discussionId']);
        $this->assertEmpty(array_filter($resourceBody['included'] ?? [], fn ($item) => $item['type'] === 'discussions'));
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['discussion_id' => 1]))->getStatusCode());
    }

    public function test_visibility_scopes_are_respected_by_creation_and_serialization()
    {
        $this->extend((new Extend\ModelVisibility(Discussion::class))->scope(function (User $actor, Builder $query) {
            if (!$actor->isAdmin()) {
                $query->where('discussions.id', '!=', 1);
            }
        }));
        $response = $this->send($this->request('GET', '/api/trader/feedback'));
        $body = json_decode((string) $response->getBody(), true);
        $this->assertEmpty(array_filter($body['included'], fn ($item) => $item['type'] === 'discussions'));
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback', $this->validFeedback(['discussion_id' => 1]))->getStatusCode());
    }

    public function test_user_includes_do_not_disclose_pending_feedback()
    {
        $response = $this->send($this->request('GET', '/api/users/3')->withQueryParams(['include' => 'feedbacksReceived']));
        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $feedbacks = array_filter($body['included'] ?? [], fn ($item) => $item['type'] === 'trader-feedbacks');
        $this->assertNotEmpty($feedbacks);
        foreach ($feedbacks as $feedback) {
            $this->assertTrue($feedback['attributes']['isApproved']);
        }
        $this->assertArrayNotHasKey('pendingFeedbackCount', $body['data']['attributes']);
    }

    public function test_moderation_counters_are_distinct_and_batched_for_user_lists()
    {
        $users = [];
        for ($id = 5; $id <= 20; $id++) {
            $users[] = array_merge($this->normalUser(), ['id' => $id, 'username' => 'member'.$id, 'email' => 'member'.$id.'@machine.local']);
        }
        $this->prepareDatabase([
            User::class   => $users,
            'tfb_reports' => [['id' => 1, 'user_id' => 4, 'feedback_id' => 1, 'reason' => 'Needs review.', 'resolved' => false]],
        ]);
        $response = $this->send($this->request('GET', '/api/users', ['authenticatedAs' => 1])->withQueryParams(['page' => ['limit' => 20]]));
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $body = json_decode((string) $response->getBody(), true);
        $recipient = array_values(array_filter($body['data'], fn ($item) => $item['id'] === '3'))[0];
        $this->assertSame(1, $recipient['attributes']['traderFeedbackCount']);
        $this->assertSame(1, $recipient['attributes']['pendingFeedbackCount']);
        $this->assertSame(1, $recipient['attributes']['pendingReportCount']);
    }

    public function test_report_permission_and_visibility_are_required()
    {
        $this->assertSame(403, $this->mutate('POST', '/api/trader/feedback/1/report', ['reason' => 'A reason.'])->getStatusCode());
        $this->assertSame(403, $this->mutate('POST', '/api/trader/feedback/2/report', ['reason' => 'A reason.'], 2)->getStatusCode());
    }

    public function test_report_creation_validation_rate_limit_and_resolved_duplicates()
    {
        $this->prepareDatabase(['group_permission' => [['group_id' => 3, 'permission' => 'huseyinfiliz-traderfeedback.report']]]);
        $path = '/api/trader/feedback/1/report';
        $this->assertSame(422, $this->mutate('POST', $path, ['reason' => ''])->getStatusCode());
        $this->assertSame(422, $this->mutate('POST', $path, ['reason' => str_repeat('x', 1001)])->getStatusCode());
        $response = $this->mutate('POST', $path, ['reason' => '<b>This needs review.</b>']);
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        $report = $this->database()->table('tfb_reports')->first();
        $this->assertSame('This needs review.', $report->reason);
        $this->database()->table('tfb_reports')->where('id', $report->id)->update(['resolved' => true]);
        $this->assertSame(422, $this->mutate('POST', $path, ['reason' => 'Another reason.'])->getStatusCode());
    }

    public function test_reports_are_rate_limited_across_different_feedback()
    {
        $this->prepareDatabase(['group_permission' => [['group_id' => 3, 'permission' => 'huseyinfiliz-traderfeedback.report']]]);
        $this->app();
        Feedback::whereKey(2)->update(['is_approved' => true]);
        $this->assertSame(201, $this->mutate('POST', '/api/trader/feedback/1/report', ['reason' => 'Needs review.'])->getStatusCode());
        $this->assertSame(422, $this->mutate('POST', '/api/trader/feedback/2/report', ['reason' => 'Also needs review.'])->getStatusCode());
        $this->assertSame(1, $this->database()->table('tfb_reports')->count());
    }

    public function test_participant_selection_respects_discussion_visibility()
    {
        $response = $this->send($this->request('GET', '/api/trader/discussions/1/participants', ['authenticatedAs' => 4]));
        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertContains('2', array_column($body['data'], 'id'));
        Discussion::whereKey(1)->update(['hidden_at' => Carbon::now()]);
        $response = $this->send($this->request('GET', '/api/trader/discussions/1/participants', ['authenticatedAs' => 4]));
        $this->assertSame(404, $response->getStatusCode());
    }

    public function test_participant_selection_requires_registration_and_feedback_permission()
    {
        $path = '/api/trader/discussions/1/participants';
        $this->prepareDatabase([
            User::class => [array_merge($this->normalUser(), [
                'id'                 => 5,
                'username'           => 'unconfirmed',
                'email'              => 'unconfirmed@machine.local',
                'is_email_confirmed' => 0,
            ])],
        ]);
        $this->assertSame(401, $this->send($this->request('GET', $path))->getStatusCode());
        $this->assertSame(403, $this->send($this->request('GET', $path, ['authenticatedAs' => 5]))->getStatusCode());
    }

    public function test_participant_selection_rejects_confirmed_users_without_feedback_permission()
    {
        $this->app();
        $this->database()->table('group_permission')->where('permission', 'huseyinfiliz-traderfeedback.give')->delete();
        $response = $this->send($this->request('GET', '/api/trader/discussions/1/participants', ['authenticatedAs' => 4]));
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_participant_selection_only_uses_visible_comments()
    {
        $this->prepareDatabase([
            Post::class => [[
                'id'            => 2,
                'discussion_id' => 1,
                'user_id'       => 3,
                'type'          => 'comment',
                'content'       => '<t><p>Hidden comment</p></t>',
                'number'        => 2,
                'created_at'    => Carbon::now()->toDateTimeString(),
                'hidden_at'     => Carbon::now()->toDateTimeString(),
            ]],
        ]);
        $path = '/api/trader/discussions/1/participants';
        $response = $this->send($this->request('GET', $path, ['authenticatedAs' => 4]));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['2'], array_column(json_decode((string) $response->getBody(), true)['data'], 'id'));

        $response = $this->send($this->request('GET', $path, ['authenticatedAs' => 1]));
        $this->assertContains('3', array_column(json_decode((string) $response->getBody(), true)['data'], 'id'));
    }

    public function test_participant_selection_respects_extension_post_visibility()
    {
        $this->extend((new Extend\ModelVisibility(Post::class))->scope(function (User $actor, Builder $query) {
            $query->where('posts.id', '!=', 1);
        }));
        $response = $this->send($this->request('GET', '/api/trader/discussions/1/participants', ['authenticatedAs' => 4]));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], json_decode((string) $response->getBody(), true)['data']);
    }

    public function test_participant_selection_respects_extension_user_visibility()
    {
        $this->extend((new Extend\ModelVisibility(User::class))->scope(function (User $actor, Builder $query) {
            $query->where('users.id', '!=', 2);
        }));
        $response = $this->send($this->request('GET', '/api/trader/discussions/1/participants', ['authenticatedAs' => 4]));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], json_decode((string) $response->getBody(), true)['data']);
    }

    public function test_moderator_notifications_preserve_all_batches_and_reconcile_existing_recipients()
    {
        $batchSizes = [];
        $this->extend((new Extend\Notification())->beforeSending(function (BlueprintInterface $blueprint, array $recipients) use (&$batchSizes) {
            $batchSizes[] = count($recipients);

            return array_filter($recipients, fn (User $user) => $user->id !== 404);
        }));
        $users = [];
        for ($id = 5; $id <= 405; $id++) {
            $users[] = array_merge($this->normalUser(), [
                'id'                 => $id,
                'username'           => 'moderator'.$id,
                'email'              => 'moderator'.$id.'@machine.local',
                'is_email_confirmed' => $id !== 405,
            ]);
        }
        $this->prepareDatabase([
            User::class        => $users,
            'group_permission' => [['group_id' => 3, 'permission' => 'huseyinfiliz-traderfeedback.moderate']],
        ]);
        $this->setting('huseyinfiliz.traderfeedback.requireApproval', true);
        $response = $this->mutate('POST', '/api/trader/feedback', $this->validFeedback());
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());

        $feedback = Feedback::latest('id')->firstOrFail();
        $blueprint = new FeedbackNeedsApprovalBlueprint($feedback);
        $active = fn () => Notification::matchingBlueprint($blueprint)->where('is_deleted', false);
        $this->assertSame([200, 200, 3], $batchSizes);
        $this->assertSame(402, $active()->count());
        foreach ([1, 2, 3, 200, 201, 400, 403] as $id) {
            $this->assertTrue($active()->where('user_id', $id)->exists(), 'Missing moderator '.$id);
        }
        foreach ([4, 404, 405] as $id) {
            $this->assertFalse($active()->where('user_id', $id)->exists());
        }

        $syncer = $this->app()->getContainer()->make(ModeratorNotificationSyncer::class);
        $syncer->syncModerators($blueprint, 4);
        $this->assertSame(402, $active()->count());
        $this->assertSame(402, Notification::matchingBlueprint($blueprint)->count());

        $syncer->syncModerators($blueprint, 3);
        $this->assertSame(402, $active()->count());
        $this->assertFalse($active()->where('user_id', 3)->exists());
        $this->assertTrue($active()->where('user_id', 4)->exists());
        $this->assertTrue($active()->where('user_id', 1)->exists());
    }

    public function test_moderation_actions_are_recorded_when_audit_is_enabled()
    {
        $this->extension('flarum-audit');
        $this->assertSame(200, $this->mutate('POST', '/api/trader/feedback/2/approve', [], 1)->getStatusCode());
        $this->assertTrue($this->database()->table('audit_log')->where('action', 'traderfeedback.approved')->where('actor_id', 1)->exists());
        $this->assertSame(204, $this->mutate('DELETE', '/api/trader/feedback/1', [], 1)->getStatusCode());
        $this->assertTrue($this->database()->table('audit_log')->where('action', 'traderfeedback.deleted')->where('actor_id', 1)->exists());
    }

    public function test_profiles_and_posts_include_the_stats_needed_for_badges()
    {
        $stats = $this->app()->getContainer()->make(StatsService::class);
        $stats->updateUserStats(2);
        $stats->updateUserStats(3);
        foreach (['/api/users/3', '/api/posts/1', '/api/discussions/1'] as $path) {
            $response = $this->send($this->request('GET', $path));
            $this->assertSame(200, $response->getStatusCode(), $path);
            $body = json_decode((string) $response->getBody(), true);
            $this->assertNotEmpty(array_filter($body['included'] ?? [], fn ($item) => $item['type'] === 'trader-stats'), $path);
        }
    }

    private function validFeedback(array $overrides = []): array
    {
        return array_merge(['to_user_id' => 3, 'type' => 'positive', 'role' => 'buyer', 'comment' => 'A successful trade with this member.'], $overrides);
    }

    private function mutate(string $method, string $path, array $attributes, int $actor = 4)
    {
        return $this->send($this->requestWithJsonBody(
            $this->request($method, $path, ['authenticatedAs' => $actor]),
            ['data' => ['attributes' => $attributes]]
        ));
    }
}
