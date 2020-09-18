<?php

namespace Tests\Feature\Message\Restore;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageRestoreForSenderTest extends TestCase
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
		$secondMessage->restoreForUser($sender);
		$secondMessage->refresh();

		$this->assertFalse($secondMessage->trashed());

		$secondMessage->refresh();

		$recepientParticipation = $secondMessage->getFirstRecepientParticipation();

		$this->assertEquals(2, $recepientParticipation->new_messages_count);
		$this->assertEquals(null, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(2, $recepient->getNewMessagesCount());

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

		$secondMessage->deleteForUser($sender);
		$secondMessage->refresh();
		$secondMessage->restoreForUser($sender);
		$secondMessage->refresh();

		$userDeletition = $secondMessage->user_deletetions()->first();

		$this->assertNull($userDeletition);

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
		$message->restoreForUser($sender);
		$message->refresh();

		$this->assertFalse($message->trashed());

		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(null, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
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
		$message->restoreForUser($sender);
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

	public function testRestore()
	{
		$sender = factory(User::class)
			->create();

		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		// delete
		$this->actingAs($sender)
			->delete(route('messages.destroy', $message))
			->assertOk();

		// restore
		$this->actingAs($sender)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertFalse($message->isViewed());

		$recepientParticipation = $recepient->participations()->first();
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$senderParticipation = $sender->participations()->first();
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testRestoreWithTwoMessages()
	{
		$sender = factory(User::class)
			->create();

		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id,
				'created_at' => $message->created_at->addSeconds(3)
			]);

		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());

		$senderParticipation = $sender->participations()->first();
		$this->assertEquals($message2->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $recepient->participations()->first();
		$this->assertEquals($message2->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(2, $recepientParticipation->new_messages_count);
		$this->assertEquals(2, $recepient->getNewMessagesCount());

		$this->actingAs($sender)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$message->refresh();

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		// restore
		$this->actingAs($sender)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$senderParticipation = $sender->participations()->first();
		$this->assertEquals($message2->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $recepient->participations()->first();
		$this->assertEquals($message2->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(2, $recepientParticipation->new_messages_count);
		$this->assertEquals(2, $recepient->getNewMessagesCount());
	}

	public function testRestoreViewed()
	{
		$sender = factory(User::class)
			->create();

		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		// delete
		$this->actingAs($sender)
			->delete(route('messages.destroy', $message))
			->assertOk();

		// restore
		$this->actingAs($sender)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$senderParticipation = $sender->participations()->first();

		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $recepient->participations()->first();

		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals(0, $recepient->getNewMessagesCount());
	}
}
