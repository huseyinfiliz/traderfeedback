<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Integration;

use Carbon\Carbon;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;

class TraderStatsApiTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('huseyinfiliz-traderfeedback');

        $this->prepareDatabase([
            'users' => [
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
        $this->assertSame(4, $body['data']['attributes']['positive_count']);
        $this->assertSame(1, $body['data']['attributes']['neutral_count']);
        $this->assertSame(0, $body['data']['attributes']['negative_count']);
        $this->assertSame(80.0, (float) $body['data']['attributes']['score']);
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
