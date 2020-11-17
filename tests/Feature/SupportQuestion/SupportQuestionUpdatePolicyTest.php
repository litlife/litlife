<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionUpdatePolicyTest extends TestCase
{
    public function testCanIfUserCreator()
    {
        $question = SupportQuestion::factory()->create();

        $user = $question->create_user;

        $this->assertTrue($user->can('update', $question));
    }

    public function testCanIfCanReplyToSupport()
    {
        $question = SupportQuestion::factory()->create();

        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $this->assertTrue($user->can('update', $question));
    }

    public function testCantIfOtherUser()
    {
        $question = SupportQuestion::factory()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('update', $question));
    }
}
