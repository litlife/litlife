<?php

namespace Tests\Feature\Message\Delete;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageDeletePolicyTest extends TestCase
{
	public function testPolicy()
	{
		$recepient = User::factory()->create();

		$other_user = User::factory()->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $recepient->id
			]);

		$this->assertTrue($recepient->can('delete', $message));
		$this->assertFalse($recepient->can('restore', $message));

		$this->assertTrue($message->create_user->can('delete', $message));
		$this->assertFalse($message->create_user->can('restore', $message));

		$this->assertFalse($other_user->can('delete', $message));
		$this->assertFalse($other_user->can('restore', $message));

		$message->deleteForUser($recepient);

		$message = Message::joinUserDeletions($recepient->id)->findOrFail($message->id);
		$this->assertTrue($recepient->can('restore', $message));
		$this->assertFalse($recepient->can('delete', $message));

		$message = Message::joinUserDeletions($message->create_user->id)->findOrFail($message->id);
		$this->assertTrue($message->create_user->can('delete', $message));
		$this->assertFalse($message->create_user->can('restore', $message));

		$this->assertFalse($other_user->can('delete', $message));
		$this->assertFalse($other_user->can('restore', $message));
	}
}
