<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionIndexTest extends TestCase
{
	public function testRedirectIfNoSupportQuestionCreated()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('users.support_questions.index', ['user' => $user]))
			->assertRedirect(route('support_questions.create', ['user' => $user]));
	}

	public function testIsOk()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('with_message')
			->create();

		$user = $supportQuestion->create_user;

		$this->actingAs($user)
			->get(route('users.support_questions.index', ['user' => $user]))
			->assertOk();
	}
}
