<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Integration;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;

class TraderStatsApiTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('huseyinfiliz-traderfeedback');

        $this->prepareDatabase([
            User::class => [
                $this->normalUser(), // id 2
                [
                    'id'                 => 3,
                    'username'           => 'trader',
                    'password'           => '$2y$10$LO59tiT7uggl6Oe23o/O6.utnF6ipngYjvMvaxo1TciKqBttDNKim',
                    'email'              => 'trader@machine.local',
                    'is_email_confirmed' => 1,
                ],
            ],
            'group_user' => [
                ['user_id' => 1, 'group_id' => 1],
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
                    'id'           => 1,
                    'from_user_id' => 2,
                    'to_user_id'   => 3,
                    'type'         => 'positive',
                    'role'         => 'buyer',
                    'comment'      => 'Positive feedback 1',
                    'discussion_id'=> 1,
                    'is_approved'  => 1,
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ],
                [
                    'id'           => 2,
                    'from_user_id' => 2,
                    'to_user_id'   => 3,
                    'type'         => 'positive',
                    'role'         => 'buyer',
                    'comment'      => 'Positive feedback 2',
                    'discussion_id'=> 1,
                    'is_approved'  => 1,
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ],
                [
                    'id'           => 3,
                    'from_user_id' => 2,
                    'to_user_id'   => 3,
                    'type'         => 'positive',
                    'role'         => 'buyer',
                    'comment'      => 'Positive feedback 3',
                    'discussion_id'=> 1,
                    'is_approved'  => 1,
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ],
                [
                    'id'           => 4,
                    'from_user_id' => 2,
                    'to_user_id'   => 3,
                    'type'         => 'positive',
                    'role'         => 'buyer',
                    'comment'      => 'Positive feedback 4',
                    'discussion_id'=> 1,
                    'is_approved'  => 1,
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ],
                [
                    'id'           => 5,
                    'from_user_id' => 2,
                    'to_user_id'   => 3,
                    'type'         => 'neutral',
                    'role'         => 'buyer',
                    'comment'      => 'Neutral feedback 5',
                    'discussion_id'=> 1,
                    'is_approved'  => 1,
                    'created_at'   => Carbon::now()->toDateTimeString(),
                    'updated_at'   => Carbon::now()->toDateTimeString(),
                ],
            ],
            'tfb_stats' => [
                [
                    'id'             => 1,
                    'user_id'        => 3,
                    'positive_count' => 4,
                    'neutral_count'  => 1,
                    'negative_count' => 0,
                    'score'          => 80.0,
                    'last_updated'   => Carbon::now()->toDateTimeString(),
                ],
            ],
        ]);
    }

    public function test_guest_can_fetch_trader_stats()
    {
        $response = $this->send($this->request('GET', '/api/trader/stats/3'));
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(4, $body['data']['attributes']['positiveCount']);
        $this->assertSame(1, $body['data']['attributes']['neutralCount']);
        $this->assertSame(0, $body['data']['attributes']['negativeCount']);
        $this->assertSame(80.0, (float) $body['data']['attributes']['score']);
    }

    public function test_nonexistent_user_returns_not_found_without_writing_stats()
    {
        $response = $this->send($this->request('GET', '/api/trader/stats/999999'));
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(1, $this->database()->table('tfb_stats')->count());
    }

    public function test_missing_stats_are_read_without_creating_a_database_record()
    {
        $this->app();
        $this->database()->table('tfb_stats')->delete();
        $response = $this->send($this->request('GET', '/api/trader/stats/3'));
        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(4, $body['data']['attributes']['positiveCount']);
        $this->assertSame(0, $this->database()->table('tfb_stats')->count());
    }

    public function test_stats_summary_requires_moderate_permission()
    {
        // Guest cannot view summary
        $response = $this->send($this->request('GET', '/api/trader/stats/summary'));
        $this->assertSame(403, $response->getStatusCode());

        // Normal user cannot view summary
        $response = $this->send(
            $this->request('GET', '/api/trader/stats/summary', ['authenticatedAs' => 2])
        );
        $this->assertSame(403, $response->getStatusCode());

        // Admin can view summary
        $response = $this->send(
            $this->request('GET', '/api/trader/stats/summary', ['authenticatedAs' => 1])
        );
        $this->assertSame(200, $response->getStatusCode());
    }
}
