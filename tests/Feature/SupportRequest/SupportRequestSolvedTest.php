<?php

namespace Tests\Feature\SupportRequst;

use App\User;
use Tests\TestCase;

class SupportRequestSolvedTest extends TestCase
{
	public function testIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_requests.solved'))
			->assertOk();
	}
}
