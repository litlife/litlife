<?php

namespace Tests\Feature\Message;

use App\Conversation;
use App\Message;
use Tests\TestCase;

class MessageNotDeletedForUserScopeTest extends TestCase
{
	public function testFoundMessageIfNotDeleted()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();

		//$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		//$recepient = $recepientParticipation->user;

		$this->assertEquals(1, Message::where('id', $message->id)->notDeletedForUser($sender)->count());
	}

	public function testNotFoundMessageIfDeleted()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();

		$sender = $message->create_user;

		$message->delete();

		$this->assertEquals(0, Message::where('id', $message->id)->notDeletedForUser($sender)->count());
	}

	public function testIfDeletedForSender()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();

		$sender = $message->create_user;
		$recepient = $message->getFirstRecepientParticipation()->user;

		$message->user_deletetions()
			->firstOrCreate(
				['user_id' => $sender->id],
				['deleted_at' => now()]
			);

		$this->assertEquals(0, Message::where('id', $message->id)->notDeletedForUser($sender)->count());
		$this->assertEquals(1, Message::where('id', $message->id)->notDeletedForUser($recepient)->count());
	}

	public function testIfDeletedForRecepient()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();

		$sender = $message->create_user;
		$recepient = $message->getFirstRecepientParticipation()->user;

		$message->user_deletetions()
			->firstOrCreate(
				['user_id' => $recepient->id],
				['deleted_at' => now()]
			);

		$this->assertEquals(1, Message::where('id', $message->id)->notDeletedForUser($sender)->count());
		$this->assertEquals(0, Message::where('id', $message->id)->notDeletedForUser($recepient)->count());
	}
}
