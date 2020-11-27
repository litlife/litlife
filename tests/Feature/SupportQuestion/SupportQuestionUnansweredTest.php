<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionUnansweredTest extends TestCase
{
    public function testIsOk()
    {
        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $request = SupportQuestion::factory()
            ->with_message()
            ->create();

        $this->actingAs($user)
            ->get(route('support_questions.unsolved'))
            ->assertOk()
            ->assertViewHas('supportQuestions');
    }
}
