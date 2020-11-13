<?php

namespace Tests\Feature\SupportQuestion\Feedback;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionFeedbackStorePolicyTest extends TestCase
{
    public function testCanIfQuestionAccepted()
    {
        $question = SupportQuestion::factory()->accepted()->create();

        $user = $question->create_user;

        $this->assertTrue($user->can('create_feedback', $question));
    }

    public function testCantIfQuestionNotAccepted()
    {
        $question = SupportQuestion::factory()->sent_for_review()->create();

        $user = $question->create_user;

        $this->assertFalse($user->can('create_feedback', $question));
    }

    public function testCantIfUserNotCreator()
    {
        $question = SupportQuestion::factory()->accepted()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('create_feedback', $question));
    }
}
