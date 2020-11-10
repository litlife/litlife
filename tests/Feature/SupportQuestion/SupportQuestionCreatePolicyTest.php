<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionCreatePolicyTest extends TestCase
{
	public function testCanIfHasPermissions()
	{
		$user = User::factory()->create();
		$user->group->send_a_support_question = true;
		$user->push();

		$this->assertTrue($user->can('create', SupportQuestion::class));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$user = User::factory()->create();
		$user->group->send_a_support_question = false;
		$user->push();

		$this->assertFalse($user->can('create', SupportQuestion::class));
	}
}
