<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequestMessage;
use Tests\TestCase;

class SupportRequestShowTest extends TestCase
{
	public function testShow()
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
}
