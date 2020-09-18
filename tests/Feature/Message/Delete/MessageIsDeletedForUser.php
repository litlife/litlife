<?php

namespace Tests\Feature\Message\Delete;

use App\Message;
use App\MessageDelete;
use App\User;
use Tests\TestCase;

class MessageIsDeletedForUser extends TestCase
{
	public function testTrueIfFoundInUserDeletitions()
	{
		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create(['recepient_id' => $recepient->id]);

		$deletion = factory(MessageDelete::class)
			->create([
				'user_id' => $recepient->id,
				'message_id' => $message->id,
			]);

		$this->assertTrue($message->isDeletedForUser($recepient));
	}

	public function testFalseIfFoundInUserDeletitions()
	{
		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create(['recepient_id' => $recepient->id]);

		$deletion = factory(MessageDelete::class)
			->create([
				'user_id' => $recepient->id,
				'message_id' => $message->id,
			]);

		$user = factory(User::class)
			->create();

		$this->assertFalse($message->isDeletedForUser($user));
	}
}
