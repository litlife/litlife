<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequestMessage;
use App\User;
use Tests\TestCase;

class SupportRequestShowTest extends TestCase
{
	public function testShowIfAuthUserCreator()
	{
		$request = factory(SupportRequestMessage::class)
			->create();

		$request = $request->supportRequest;

		$user = $request->create_user;

		$this->actingAs($user)
			->get(route('support_requests.show', ['support_request' => $request->id]))
			->assertOk()
			->assertViewHas('supportRequest', $request)
			->assertViewHas('messages');
	}

	public function testShowIfAuthUserNotCreator()
	{
		$request = factory(SupportRequestMessage::class)
			->create();

		$request = $request->supportRequest;

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_requests.show', ['support_request' => $request->id]))
			->assertOk()
			->assertViewHas('supportRequest', $request)
			->assertViewHas('messages');
	}
}
