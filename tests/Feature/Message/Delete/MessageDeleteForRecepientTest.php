<?php

namespace Tests\Feature\Message\Delete;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageDeleteForRecepientTest extends TestCase
{
	public function testIfMessageNotViewed()
	{
		$conversation = Conversation::factory()->with_two_not_viewed_message()->create();

		$firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
		$secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$sender = $secondMessage->create_user;
		$recepient = $recepientParticipation->user;

		$secondMessage->deleteForUser($recepient);
		$secondMessage->refresh();

		$this->assertTrue($secondMessage->trashed());

		$firstMessage->refresh();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($firstMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$senderParticipation = $firstMessage->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($firstMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfMessageViewed()
	{
		$conversation = Conversation::factory()->with_two_viewed_message()->create();

		$firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
		$secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$sender = $secondMessage->create_user;
		$recepient = $recepientParticipation->user;

		$secondMessage->deleteForUser($recepient);
		$secondMessage->refresh();

		$userDeletition = $secondMessage->user_deletetions()->first();

		$this->assertNotNull($userDeletition);
		$this->assertEquals($userDeletition->user_id, $recepient->id);
		$this->assertEquals($userDeletition->message_id, $secondMessage->id);
		$this->assertNotNull($userDeletition->deleted_at);

		$firstMessage->refresh();

		$recepientParticipation = $firstMessage->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($firstMessage->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $firstMessage->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($secondMessage->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfNotViewedAndLatest()
	{
		$conversation = Conversation::factory()->with_not_viewed_message()->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		$recepient = $recepientParticipation->user;

		$message->deleteForUser($recepient);
		$message->refresh();

		$this->assertTrue($message->trashed());

		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals(null, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(null, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testIfViewedAndLatest()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$sender = $message->create_user;
		$recepient = $recepientParticipation->user;

		$message->deleteForUser($recepient);
		$message->refresh();

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals(null, $recepientParticipation->latest_message_id);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals(0, $sender->getNewMessagesCount());
	}

	public function testDeleteNotViewed()
	{
		$sender = User::factory()->create();

		$recepient = User::factory()->create();

		$message = factory(Message::class)
			->states('not_viewed')
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		$this->actingAs($recepient)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$message->refresh();

		$this->assertTrue($message->isViewed());

		$senderParticipation = $sender->participations()->first();

		$this->assertNull($senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $recepient->participations()->first();

		$this->assertNull($recepientParticipation->latest_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals(0, $recepient->getNewMessagesCount());
	}

	public function testDeleteViewed()
	{
		$sender = User::factory()->create();

		$recepient = User::factory()->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $sender->id,
				'recepient_id' => $recepient->id
			]);

		$response = $this->actingAs($recepient)
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
		$this->assertNull($recepientParticipation->latest_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$message = Message::joinUserDeletions($recepient->id)
			->findOrFail($message->id);

		$this->assertNotNull($message->message_deletions_deleted_at);
		$this->assertNotNull($response->decodeResponseJson()['deleted_at']);
	}

	public function testHttpDeleteNotViewedMessage()
	{
		$recepient = User::factory()->create();

		$message = Message::factory()->create(['recepient_id' => $recepient->id]);

		$response = $this->actingAs($recepient)
			->delete(route('messages.destroy', $message->id))
			->assertOk()
			->assertSee('deleted_at');

		$message = Message::withTrashed()
			->findOrFail($message->id);

		$this->assertNotNull($message->deleted_at);
		$this->assertNotNull($response->decodeResponseJson()['deleted_at']);
	}
}
