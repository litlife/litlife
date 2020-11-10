<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageUserInboxTest extends TestCase
{
	public function test401UsersInbox()
	{
		$user = User::factory()->create();

		$this->get(route('users.inbox', ['user' => $user]))
			->assertStatus(401);
	}

	public function testInboxPreviewText()
	{
		$auth_user = User::factory()->create();
		$user = User::factory()->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $auth_user->id,
				'recepient_id' => $user->id
			]);

		$message2 = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $auth_user->id,
				'create_user_id' => $user->id,
				'bb_text' => '[quote]text2[/quote]text'
			]);

		$this->actingAs($user)
			->get(route('users.inbox', $user))
			->assertOk()
			->assertSeeText(__('common.you') . ': text');
	}
}
