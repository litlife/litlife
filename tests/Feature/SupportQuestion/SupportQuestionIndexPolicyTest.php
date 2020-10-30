<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionIndexPolicyTest extends TestCase
{
	public function testCanIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('view_index', SupportQuestion::class));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('view_index', SupportQuestion::class));
	}
}
