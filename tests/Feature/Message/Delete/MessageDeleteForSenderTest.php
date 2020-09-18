<?php

namespace Tests\Feature\Message\Delete;

use App\Conversation;
use App\Message;
use App\User;
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

	public function testRouteIsOk()
	{
		$sender = factory(User::class)
			->create();

		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->states('not_viewed')
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		// delete
		$this->actingAs($sender)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertFalse($message->isViewed());

		$recepientParticipation = $recepient->participations()->first();
		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertNull($recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $sender->participations()->first();
		$this->assertNull($senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testRouteIsOkWithTwoMessages()
	{
		$conversation = factory(Conversation::class)
			->states('with_two_not_viewed_message')
			->create();

		$firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
		$secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$sender = $secondMessage->create_user;
		$recepient = $recepientParticipation->user;

		$this->actingAs($sender)
			->delete(route('messages.destroy', $secondMessage))
			->assertOk();

		$firstMessage->refresh();

		$this->assertFalse($firstMessage->isViewed());

		$senderParticipation = $firstMessage->getSenderParticipation();

		$this->assertEquals($firstMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$this->assertEquals($firstMessage->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient->getNewMessagesCount());
	}

	public function testViewedAndNotViewed()
	{
		$sender = factory(User::class)
			->create();

		$recepient = factory(User::class)
			->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $recepient->id,
				'recepient_id' => $sender->id
			]);

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		$this->assertTrue($message->isViewed());
		$this->assertFalse($message2->isViewed());

		$senderParticipation = $message->getSenderParticipation();
		$senderParticipation->latest_seen_message_id = $message2->id;
		$senderParticipation->save();

		$recepientParticipation = $message->getFirstRecepientParticipation();
		$recepientParticipation->latest_seen_message_id = $message->id;
		$recepientParticipation->save();

		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);

		$message2->deleteForUser($sender);

		$recepientParticipation->updateNewMessagesCount();
		$recepientParticipation->updateLatestMessage();
		$recepientParticipation->save();

		$senderParticipation->updateNewMessagesCount();
		$senderParticipation->updateLatestMessage();
		$senderParticipation->save();

		$message->refresh();
		$message2->refresh();

		$senderParticipation = $message->getSenderParticipation();
		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals(0, $senderParticipation->new_messages_count);

		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);

		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
	}

	public function testRouteIsOkViewed()
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

		$this->actingAs($sender)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$senderParticipation = $sender->participations()->first();

		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertNull($senderParticipation->latest_message_id);
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
