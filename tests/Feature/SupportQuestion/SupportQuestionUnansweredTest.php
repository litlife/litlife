<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionUnsolvedTest extends TestCase
{
	public function testIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$request = factory(SupportQuestion::class)
			->states('with_message')
			->create();

		$this->actingAs($user)
			->get(route('support_questions.unsolved'))
			->assertOk()
			->assertViewHas('supportQuestions');
	}
}
