<?php

namespace Tests\Feature\SupportQuestion;

use App\User;
use Tests\TestCase;

class SupportQuestionCreateTest extends TestCase
{
    public function testIsOk()
    {
        $user = User::factory()->create();
        $user->group->send_a_support_question = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('support_questions.create', ['user' => $user]))
            ->assertOk();
    }
}
