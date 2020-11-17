<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionEditTest extends TestCase
{
    public function testEditIsOk()
    {
        $question = SupportQuestion::factory()->create();

        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('support_questions.edit', $question))
            ->assertOk();
    }
}
