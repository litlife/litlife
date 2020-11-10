<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionShowPolicyTest extends TestCase
{
	public function testCanIfUserCreator()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = $supportQuestion->create_user;

		$this->assertTrue($user->can('show', $supportQuestion));
	}

	public function testCanIfHasPermissionToReply()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = User::factory()->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('show', $supportQuestion));
	}

	public function testCantIfUserNotCreator()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('show', $supportQuestion));
	}

	public function testCantIfHasDoesntHavePermissionToReply()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = User::factory()->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('show', $supportQuestion));
	}
}
