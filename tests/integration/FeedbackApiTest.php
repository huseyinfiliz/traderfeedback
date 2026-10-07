<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Integration;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;

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

    public function test_author_can_view_own_pending_feedback()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback/2', [
                'authenticatedAs' => 2,
            ])
        );
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_recipient_can_view_own_pending_feedback()
    {
        $response = $this->send(
            $this->request('GET', '/api/trader/feedback/2', [
                'authenticatedAs' => 3,
            ])
        );
        $this->assertSame(200, $response->getStatusCode());
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
}
