<?php

namespace Tests\Feature\SupportRequest;

use App\User;
use Tests\TestCase;

class SupportRequestCreateTest extends TestCase
{
	public function testIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->send_a_support_request = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_requests.create'))
			->assertOk();
	}
}
