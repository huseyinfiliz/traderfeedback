<?php

namespace HuseyinFiliz\TraderFeedback\Tests\Unit;

use Carbon\Carbon;
use Flarum\Testing\unit\TestCase;
use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Access\FeedbackPolicy;
use HuseyinFiliz\TraderFeedback\Models\Feedback;

class TestUser extends User
{
    public ?array $permissions = [];

    public function __construct(int $id = 0, array $permissions = [])
    {
        parent::__construct();
        $this->id = $id;
        $this->permissions = $permissions;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }
}

class FeedbackPolicyTest extends TestCase
{
    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new FeedbackPolicy();
    }

    protected function createUser(int $id, array $permissions = []): TestUser
    {
        return new TestUser($id, $permissions);
    }

    public function test_anyone_can_view_approved_feedback()
    {
        $guest = $this->createUser(0);

        $feedback = new Feedback();
        $feedback->is_approved = true;
        $feedback->from_user_id = 10;
        $feedback->to_user_id = 20;

        $this->assertTrue($this->policy->view($guest, $feedback));
    }

    public function test_author_cannot_view_own_unapproved_feedback()
    {
        $author = $this->createUser(10);

        $feedback = new Feedback();
        $feedback->is_approved = false;
        $feedback->from_user_id = 10;
        $feedback->to_user_id = 20;

        $this->assertFalse((bool) $this->policy->view($author, $feedback));
    }

    public function test_recipient_cannot_view_own_unapproved_feedback()
    {
        $recipient = $this->createUser(20);

        $feedback = new Feedback();
        $feedback->is_approved = false;
        $feedback->from_user_id = 10;
        $feedback->to_user_id = 20;

        $this->assertFalse((bool) $this->policy->view($recipient, $feedback));
    }

    public function test_moderator_can_view_unapproved_feedback()
    {
        $mod = $this->createUser(99, ['huseyinfiliz-traderfeedback.moderate']);

        $feedback = new Feedback();
        $feedback->is_approved = false;
        $feedback->from_user_id = 10;
        $feedback->to_user_id = 20;

        $this->assertTrue($this->policy->view($mod, $feedback));
    }

    public function test_unrelated_user_cannot_view_unapproved_feedback()
    {
        $unrelated = $this->createUser(99, []);

        $feedback = new Feedback();
        $feedback->is_approved = false;
        $feedback->from_user_id = 10;
        $feedback->to_user_id = 20;

        $this->assertFalse((bool) $this->policy->view($unrelated, $feedback));
    }

    public function test_guest_cannot_view_unapproved_feedback()
    {
        $guest = $this->createUser(0, []);

        $feedback = new Feedback();
        $feedback->is_approved = false;
        $feedback->from_user_id = 10;
        $feedback->to_user_id = 20;

        $this->assertFalse((bool) $this->policy->view($guest, $feedback));
    }

    public function test_moderator_can_always_edit()
    {
        $mod = $this->createUser(99, ['huseyinfiliz-traderfeedback.moderate']);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;

        $this->assertTrue($this->policy->edit($mod, $feedback));
    }

    public function test_author_can_edit_within_24_hours()
    {
        $author = $this->createUser(10, []);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;
        $feedback->setRawAttributes([
            'from_user_id' => 10,
            'created_at'   => Carbon::now()->subHours(12),
        ], true);

        $this->assertTrue($this->policy->edit($author, $feedback));
    }

    public function test_author_cannot_edit_after_24_hours()
    {
        $author = $this->createUser(10, []);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;
        $feedback->setRawAttributes([
            'from_user_id' => 10,
            'created_at'   => Carbon::now()->subHours(25),
        ], true);

        $this->assertFalse($this->policy->edit($author, $feedback));
    }

    public function test_other_user_cannot_edit_feedback()
    {
        $other = $this->createUser(20, []);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;
        $feedback->setRawAttributes([
            'from_user_id' => 10,
            'created_at'   => Carbon::now()->subHour(),
        ], true);

        $this->assertFalse($this->policy->edit($other, $feedback));
    }

    public function test_author_can_delete_within_1_hour()
    {
        $author = $this->createUser(10, []);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;
        $feedback->setRawAttributes([
            'from_user_id' => 10,
            'created_at'   => Carbon::now()->subMinutes(30),
        ], true);

        $this->assertTrue($this->policy->delete($author, $feedback));
    }

    public function test_author_cannot_delete_after_1_hour()
    {
        $author = $this->createUser(10, []);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;
        $feedback->setRawAttributes([
            'from_user_id' => 10,
            'created_at'   => Carbon::now()->subMinutes(65),
        ], true);

        $this->assertFalse($this->policy->delete($author, $feedback));
    }

    public function test_user_with_delete_permission_can_always_delete()
    {
        $user = $this->createUser(50, ['huseyinfiliz-traderfeedback.delete']);

        $feedback = new Feedback();
        $feedback->from_user_id = 10;

        $this->assertTrue($this->policy->delete($user, $feedback));
    }
}
