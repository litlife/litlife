<?php

namespace Tests\Feature\MessageTest;

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

		$this->assertTrue($message->fresh()->isNotViewed());

		$recepient_participation = $recepient_user->participations()->first();
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		$sender_participation = $sender_user->participations()->first();
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		// delete
		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertTrue($message->fresh()->isNotViewed());

		$recepient_participation = $recepient_user->fresh()->participations()->first();
		$this->assertEquals(0, $recepient_participation->new_messages_count);
		$this->assertNull($recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(0, $recepient_user->fresh()->getNewMessagesCount());

		$sender_participation = $sender_user->fresh()->participations()->first();
		$this->assertNull($sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
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

		$this->assertTrue($message->fresh()->isNotViewed());

		$recepient_participation = $recepient_user->participations()->first();
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		$sender_participation = $sender_user->participations()->first();
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
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

		$this->assertTrue($message->isNotViewed());
		$this->assertTrue($message2->isNotViewed());

		$sender_participation = $sender_user->participations()->first();
		$this->assertEquals($message2->id, $sender_participation->latest_message_id);
		$this->assertEquals($message2->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();
		$this->assertEquals($message2->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(2, $recepient_participation->new_messages_count);
		$this->assertEquals(2, $recepient_user->fresh()->getNewMessagesCount());

		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$sender_participation = $sender_user->fresh()->participations()->first();
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->fresh()->participations()->first();
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
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

		$this->assertTrue($message->isNotViewed());
		$this->assertTrue($message2->isNotViewed());

		$sender_participation = $sender_user->participations()->first();
		$this->assertEquals($message2->id, $sender_participation->latest_message_id);
		$this->assertEquals($message2->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();
		$this->assertEquals($message2->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(2, $recepient_participation->new_messages_count);
		$this->assertEquals(2, $recepient_user->fresh()->getNewMessagesCount());

		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$sender_participation = $sender_user->fresh()->participations()->first();
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->fresh()->participations()->first();
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		// restore
		$this->actingAs($sender_user)
			->delete(route('messages.destroy', $message2))
			->assertOk();

		$sender_participation = $sender_user->participations()->first();
		$this->assertEquals($message2->id, $sender_participation->latest_message_id);
		$this->assertEquals($message2->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();
		$this->assertEquals($message2->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(2, $recepient_participation->new_messages_count);
		$this->assertEquals(2, $recepient_user->fresh()->getNewMessagesCount());
	}

	public function testDeletedForSenderNotViewed()
	{
		$auth_user = factory(User::class)
			->create();

		$user = factory(User::class)->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $auth_user->id
			])
			->fresh();

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $auth_user->id,
				'recepient_id' => $user->id
			])
			->fresh();

		$message2->deleteForUser($auth_user);

		UpdateParticipationCounters::dispatch($message->recepients_participations()->first());
		UpdateParticipationCounters::dispatch($message->sender_participation());

		$message->refresh();
		$message2->refresh();

		$this->assertEquals(0, $message->recepients_participations()->first()->new_messages_count);
		$this->assertEquals(0, $message->sender_participation()->new_messages_count);

		$this->assertEquals($message->id, $message->recepients_participations()->first()->latest_message_id);
		$this->assertEquals($message->id, $message->sender_participation()->latest_message_id);

		$this->assertEquals($message->id, $message->recepients_participations()->first()->latest_seen_message_id);
		$this->assertEquals($message->id, $message->sender_participation()->latest_seen_message_id);
	}

}