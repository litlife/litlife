<?php

namespace Tests\Feature\Message;

use App\Jobs\UpdateParticipationCounters;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageNotViewedBySenderTest extends TestCase
{
	public function testDelete()
	{
		$sender_user = factory(User::class)
			->create();

		$recepient_user = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id
			])
			->fresh();

		$this->assertFalse($message->fresh()->isViewed());

		$recepientParticipation = $recepient_user->participations()->first();
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		$senderParticipation = $sender_user->participations()->first();
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		// delete
		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertFalse($message->fresh()->isViewed());

		$recepientParticipation = $recepient_user->fresh()->participations()->first();
		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertNull($recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(0, $recepient_user->fresh()->getNewMessagesCount());

		$senderParticipation = $sender_user->fresh()->participations()->first();
		$this->assertNull($senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());
	}

	public function testRestore()
	{
		$sender_user = factory(User::class)
			->create();

		$recepient_user = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id
			])
			->fresh();

		// delete
		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		// restore
		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertFalse($message->fresh()->isViewed());

		$recepientParticipation = $recepient_user->participations()->first();
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		$senderParticipation = $sender_user->participations()->first();
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());
	}

	public function testDeleteWithTwoMessages()
	{
		$sender_user = factory(User::class)
			->create();

		$recepient_user = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id
			])
			->fresh();

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id,
				'created_at' => $message->created_at->addSeconds(3)
			])
			->fresh();

		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());

		$senderParticipation = $sender_user->participations()->first();
		$this->assertEquals($message2->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepientParticipation = $recepient_user->participations()->first();
		$this->assertEquals($message2->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(2, $recepientParticipation->new_messages_count);
		$this->assertEquals(2, $recepient_user->fresh()->getNewMessagesCount());

		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$message->refresh();

		$this->assertFalse($message->isViewed());

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());
	}

	public function testRestoreWithTwoMessages()
	{
		$sender_user = factory(User::class)
			->create();

		$recepient_user = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id
			])
			->fresh();

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id,
				'created_at' => $message->created_at->addSeconds(3)
			])
			->fresh();

		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());

		$senderParticipation = $sender_user->participations()->first();
		$this->assertEquals($message2->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepientParticipation = $recepient_user->participations()->first();
		$this->assertEquals($message2->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(2, $recepientParticipation->new_messages_count);
		$this->assertEquals(2, $recepient_user->fresh()->getNewMessagesCount());

		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$message->refresh();

		$senderParticipation = $message->getSenderParticipation();

		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		// restore
		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$senderParticipation = $sender_user->participations()->first();
		$this->assertEquals($message2->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message2->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepientParticipation = $recepient_user->participations()->first();
		$this->assertEquals($message2->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(2, $recepientParticipation->new_messages_count);
		$this->assertEquals(2, $recepient_user->fresh()->getNewMessagesCount());
	}

	public function testDeletedForSenderNotViewed()
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

		UpdateParticipationCounters::dispatch($recepientParticipation);
		UpdateParticipationCounters::dispatch($senderParticipation);

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
}