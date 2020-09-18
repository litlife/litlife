<?php

namespace Tests\Feature\Message\Restore;

use App\Conversation;
use Tests\TestCase;

class MessageRestoreForRecepientTest extends TestCase
{
	public function testIfMessageNotViewed()
	{
		$conversation = factory(Conversation::class)
			->states('with_two_not_viewed_message')
			->create();

		$firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
		$secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$sender = $secondMessage->create_user;
		$recepient = $recepientParticipation->user;

		$secondMessage->deleteForUser($recepient);
		$secondMessage->refresh();
		$secondMessage->restoreForUser($recepient);
		$secondMessage->refresh();

		$this->assertFalse($secondMessage->trashed());

		$secondMessage->refresh();

		$recepientParticipation = $secondMessage->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $secondMessage->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfMessageViewed()
	{
		$conversation = factory(Conversation::class)
			->states('with_two_viewed_message')
			->create();

		$firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
		$secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$sender = $secondMessage->create_user;
		$recepient = $recepientParticipation->user;

		$secondMessage->deleteForUser($recepient);
		$secondMessage->refresh();
		$secondMessage->restoreForUser($recepient);
		$secondMessage->refresh();

		$userDeletition = $secondMessage->user_deletetions()->first();

		$this->assertNull($userDeletition);

		$firstMessage->refresh();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $firstMessage->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfNotViewedAndLatest()
	{
		$conversation = factory(Conversation::class)
			->states('with_not_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		$recepient = $recepientParticipation->user;

		$message->deleteForUser($recepient);
		$message->refresh();
		$message->restoreForUser($recepient);
		$message->refresh();

		$this->assertFalse($message->trashed());

		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfViewedAndLatest()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		$recepient = $recepientParticipation->user;

		$message->deleteForUser($recepient);
		$message->refresh();
		$message->restoreForUser($recepient);
		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}
}
