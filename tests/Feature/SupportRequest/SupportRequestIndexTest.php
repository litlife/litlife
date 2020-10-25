<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestIndexTest extends TestCase
{
	public function testIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$request = factory(SupportRequest::class)
			->create();

		$this->actingAs($user)
			->get(route('support_requests.index'))
			->assertOk()
			->assertViewHas('supportRequests');
	}
}
