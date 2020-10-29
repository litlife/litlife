<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestIndexTest extends TestCase
{
	public function testRedirectIfNoSupportRequestCreated()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('users.support_requests.index', ['user' => $user]))
			->assertRedirect(route('support_requests.create', ['user' => $user]));
	}

	public function testIsOk()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('with_message')
			->create();

		$user = $supportRequest->create_user;

		$this->actingAs($user)
			->get(route('users.support_requests.index', ['user' => $user]))
			->assertOk();
	}
}
