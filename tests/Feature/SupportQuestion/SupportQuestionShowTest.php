<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestionMessage;
use App\User;
use Tests\TestCase;

class SupportQuestionShowTest extends TestCase
{
	public function testShowIfAuthUserCreator()
	{
		$request = factory(SupportQuestionMessage::class)
			->create();

		$request = $request->supportQuestion;

		$user = $request->create_user;

		$this->actingAs($user)
			->get(route('support_questions.show', ['support_question' => $request->id]))
			->assertOk()
			->assertViewHas('supportQuestion', $request)
			->assertViewHas('messages');
	}

	public function testShowIfAuthUserNotCreator()
	{
		$request = factory(SupportQuestionMessage::class)
			->create();

		$request = $request->supportQuestion;

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_questions.show', ['support_question' => $request->id]))
			->assertOk()
			->assertViewHas('supportQuestion', $request)
			->assertViewHas('messages');
	}
}