<?php

namespace Tests\Feature\SupportQuestion;

use App\User;
use Tests\TestCase;

class SupportQuestionGetNumberOfUnsolvedTest extends TestCase
{
	public function test()
	{
		$user = factory(User::class)
			->create();

		$this->assertIsInt($user->getNumberInProgressQuestions());
	}
}
