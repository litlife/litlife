<?php

namespace Tests\Feature\SupportQuestion;

use App\User;
use Tests\TestCase;

class SupportQuestionGetNumberOfUnansweredTest extends TestCase
{
	public function test()
	{
		$user = User::factory()->create();

		$this->assertIsInt($user->getNumberInProgressQuestions());
	}
}
