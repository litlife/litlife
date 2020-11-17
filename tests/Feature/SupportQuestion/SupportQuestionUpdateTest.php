<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionUpdateTest extends TestCase
{
    public function testUpdateIsOk()
    {
        $question = SupportQuestion::factory()->create();

        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $questionNew = SupportQuestion::factory()->make();

        $this->actingAs($user)
            ->patch(route('support_questions.update', $question), ['category' => $questionNew->category])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', __('The data was successfully stored'));
    }
}
