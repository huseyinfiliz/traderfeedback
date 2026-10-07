<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Unit;

use Flarum\Testing\unit\TestCase;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class FeedbackModelTest extends TestCase
{
    public function test_feedback_type_constants()
    {
        $this->assertSame('positive', Feedback::TYPE_POSITIVE);
        $this->assertSame('neutral', Feedback::TYPE_NEUTRAL);
        $this->assertSame('negative', Feedback::TYPE_NEGATIVE);
    }

    public function test_feedback_role_constants()
    {
        $this->assertSame('buyer', Feedback::ROLE_BUYER);
        $this->assertSame('seller', Feedback::ROLE_SELLER);
        $this->assertSame('trader', Feedback::ROLE_TRADER);
    }

    public function test_is_positive_helper()
    {
        $feedback = new Feedback(['type' => Feedback::TYPE_POSITIVE]);
        $this->assertTrue($feedback->isPositive());
        $this->assertFalse($feedback->isNeutral());
        $this->assertFalse($feedback->isNegative());
    }

    public function test_is_neutral_helper()
    {
        $feedback = new Feedback(['type' => Feedback::TYPE_NEUTRAL]);
        $this->assertTrue($feedback->isNeutral());
        $this->assertFalse($feedback->isPositive());
        $this->assertFalse($feedback->isNegative());
    }

    public function test_is_negative_helper()
    {
        $feedback = new Feedback(['type' => Feedback::TYPE_NEGATIVE]);
        $this->assertTrue($feedback->isNegative());
        $this->assertFalse($feedback->isPositive());
        $this->assertFalse($feedback->isNeutral());
    }
}
