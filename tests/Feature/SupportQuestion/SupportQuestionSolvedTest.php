<?php

namespace Tests\Feature\SupportQuestion;

use App\User;
use Tests\TestCase;

class SupportQuestionSolvedTest extends TestCase
{
	public function testIsOk()
	{
		$user = User::factory()->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_questions.solved'))
			->assertOk();
	}
}
