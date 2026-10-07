<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Unit;

use Flarum\Testing\unit\TestCase;

class StatsCalculationTest extends TestCase
{
    public function test_score_formula_with_mixed_feedbacks()
    {
        $positive = 8;
        $neutral = 1;
        $negative = 1;

        $total = $positive + $neutral + $negative;
        $score = $total > 0 ? ($positive / $total) * 100 : 0;

        $this->assertSame(10, $total);
        $this->assertEquals(80.0, $score);
    }

    public function test_score_formula_with_zero_feedbacks()
    {
        $positive = 0;
        $neutral = 0;
        $negative = 0;

        $total = $positive + $neutral + $negative;
        $score = $total > 0 ? ($positive / $total) * 100 : 0;

        $this->assertSame(0, $total);
        $this->assertEquals(0, $score);
    }

    public function test_score_formula_all_positive()
    {
        $positive = 5;
        $neutral = 0;
        $negative = 0;

        $total = $positive + $neutral + $negative;
        $score = $total > 0 ? ($positive / $total) * 100 : 0;

        $this->assertEquals(100.0, $score);
    }

    public function test_score_formula_all_negative()
    {
        $positive = 0;
        $neutral = 0;
        $negative = 5;

        $total = $positive + $neutral + $negative;
        $score = $total > 0 ? ($positive / $total) * 100 : 0;

        $this->assertEquals(0.0, $score);
    }
}
