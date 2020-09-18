<?php

namespace Tests\Feature\Message\Delete;

use App\Conversation;
use Tests\TestCase;

class MessageDeleteForSenderTest extends TestCase
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

		$secondMessage->deleteForUser($sender);
		$secondMessage->refresh();

		$this->assertTrue($secondMessage->trashed());

		$firstMessage->refresh();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(null, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($firstMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$senderParticipation = $firstMessage->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($firstMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testrIfMessageViewed()
	{
		$conversation = factory(Conversation::class)
			->states('with_two_viewed_message')
			->create();

		$firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
		$secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$sender = $secondMessage->create_user;
		$recepient = $recepientParticipation->user;

		$secondMessage->deleteForUser($sender);
		$secondMessage->refresh();

		$userDeletition = $secondMessage->user_deletetions()->first();

		$this->assertNotNull($userDeletition);
		$this->assertEquals($userDeletition->user_id, $sender->id);
		$this->assertEquals($userDeletition->message_id, $secondMessage->id);
		$this->assertNotNull($userDeletition->deleted_at);

		$firstMessage->refresh();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $firstMessage->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($firstMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfNotViewedAndItWasLatest()
	{
		$conversation = factory(Conversation::class)
			->states('with_not_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		$recepient = $recepientParticipation->user;

		$message->deleteForUser($sender);
		$message->refresh();

		$this->assertTrue($message->trashed());

		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals(null, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals(null, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(null, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfViewedAndItWasLatest()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		$recepient = $recepientParticipation->user;

		$message->deleteForUser($sender);
		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(null, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}
}
