<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Unit;

use Flarum\Testing\unit\TestCase;

class DiscussionUrlTest extends TestCase
{
    /**
     * Test extracting discussion ID from URL or numeric strings, as done in CreateFeedbackController.
     */
    public function test_extracts_id_from_flarum_discussion_url()
    {
        $urls = [
            'https://forum.example.com/d/123-some-discussion-title' => 123,
            'http://localhost/d/456' => 456,
            '/d/789-test' => 789,
            'https://community.com/d/999/5' => 999,
        ];

        foreach ($urls as $url => $expectedId) {
            $extracted = null;
            if (is_string($url) && preg_match('/\/d\/(\d+)/', $url, $matches)) {
                $extracted = (int) $matches[1];
            }
            $this->assertSame($expectedId, $extracted, "Failed extracting ID from URL: {$url}");
        }
    }

    public function test_handles_numeric_discussion_ids()
    {
        $raw = '123';
        $discussionId = is_numeric($raw) ? (int) $raw : null;
        $this->assertSame(123, $discussionId);

        $rawInt = 456;
        $discussionId = is_numeric($rawInt) ? (int) $rawInt : null;
        $this->assertSame(456, $discussionId);
    }

    public function test_extracts_id_from_discussion_participants_endpoint_path()
    {
        $path = '/api/trader/discussions/42/participants';
        $extracted = null;
        if (preg_match('/\/trader\/discussions\/(\d+)\/participants/', $path, $matches)) {
            $extracted = (int) $matches[1];
        }

        $this->assertSame(42, $extracted);
    }
}
