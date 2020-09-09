<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageNotViewedByRecepientTest extends TestCase
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

		$this->assertTrue($message->isNotViewed());

		$sender_participation = $sender_user->participations()->first();

		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();

		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals(1, $recepient_user->fresh()->getNewMessagesCount());

		// delete
		$this->actingAs($recepient_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertTrue($message->fresh()->isViewed());

		$sender_participation = $sender_user->participations()->first();

		$this->assertNull($sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();

		$this->assertNull($recepient_participation->latest_message_id);
		$this->assertEquals($message->id, $recepient_participation->latest_seen_message_id);
		$this->assertEquals(0, $recepient_participation->new_messages_count);
		$this->assertEquals(0, $recepient_user->fresh()->getNewMessagesCount());
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
		$this->actingAs($recepient_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		// restore
		$this->actingAs($recepient_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$this->assertTrue($message->fresh()->isViewed());

		$sender_participation = $sender_user->participations()->first();

		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();

		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertEquals($message->id, $recepient_participation->latest_seen_message_id);
		$this->assertEquals(0, $recepient_participation->new_messages_count);
		$this->assertEquals(0, $recepient_user->fresh()->getNewMessagesCount());
	}

	public function testMessageShouldBeAutoViewedIfDeletedByRecepient()
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

		$this->assertTrue($message->isNotViewed());

		$this->actingAs($recepient_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$message->refresh();

		$recepient_participation = $recepient_user->participations()->first();
		$this->assertEquals($message->id, $recepient_participation->latest_seen_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $recepient_participation->new_messages_count);
		$this->assertEquals(0, $recepient_user->fresh()->getNewMessagesCount());
	}
}