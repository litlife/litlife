<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class MessageUpdatePolicyTest extends TestCase
{
	public function testPolicy()
	{
		$user = factory(User::class)
			->create();

		$user2 = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create(['create_user_id' => $user->id, 'recepient_id' => $user2->id]);

		$this->assertTrue($user->can('update', $message));
		$this->assertFalse($user2->can('update', $message));

		$time = now()->addMinutes(config('litlife.time_that_can_edit_message'))->addMinute();
		Carbon::setTestNow($time);

		$this->assertFalse($user->can('update', $message));
		$this->assertFalse($user2->can('update', $message));
	}
}
