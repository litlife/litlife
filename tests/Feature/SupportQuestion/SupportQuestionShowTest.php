<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Tests\TestCase;

class SupportQuestionShowTest extends TestCase
{
    public function testShowIfAuthUserCreator()
    {
        $request = SupportQuestionMessage::factory()->create();

        $request = $request->supportQuestion;

        $user = $request->create_user;

        $this->actingAs($user)
            ->get(route('support_questions.show', ['support_question' => $request->id]))
            ->assertOk()
            ->assertViewHas('supportQuestion', $request)
            ->assertViewHas('messages')
            ->assertSeeText(__('My question is resolved'));
    }

    public function testShowIfAuthUserNotCreator()
    {
        $request = SupportQuestionMessage::factory()->create();

        $request = $request->supportQuestion;

        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('support_questions.show', ['support_question' => $request->id]))
            ->assertOk()
            ->assertViewHas('supportQuestion', $request)
            ->assertViewHas('messages');
    }

    public function testShowSolvedIfAuthUserCreator()
    {
        $request = SupportQuestionMessage::factory()
            ->for(SupportQuestion::factory()->accepted())
            ->create();

        $request = $request->supportQuestion;

        $user = $request->create_user;

        $this->actingAs($user)
            ->get(route('support_questions.show', ['support_question' => $request->id]))
            ->assertOk()
            ->assertViewHas('supportQuestion', $request)
            ->assertViewHas('messages')
            ->assertSeeText(__('Your question is resolved.'));
    }

    public function testShowSolvedIfAuthUserNotCreator()
    {
        $request = SupportQuestionMessage::factory()
            ->for(SupportQuestion::factory()->accepted())
            ->create();

        $request = $request->supportQuestion;

        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('support_questions.show', ['support_question' => $request->id]))
            ->assertOk()
            ->assertViewHas('supportQuestion', $request)
            ->assertViewHas('messages')
            ->assertDontSeeText(__('Your question is resolved.'));
    }
}
