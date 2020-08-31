<?php

namespace Tests\Feature\MessageTest;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageViewedByRecepientTest extends TestCase
{
	public function testDelete()
	{
		$sender_user = factory(User::class)
			->create();

		$recepient_user = factory(User::class)
			->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id
			])
			->fresh();

		// delete
		$this->actingAs($recepient_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$sender_participation = $sender_user->participations()->first();

		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();

		$this->assertEquals($message->id, $recepient_participation->latest_seen_message_id);
		$this->assertNull($recepient_participation->latest_message_id);
		$this->assertTrue($message->isViewed());
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
			->states('viewed')
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

		$sender_participation = $sender_user->participations()->first();

		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());

		$recepient_participation = $recepient_user->participations()->first();

		$this->assertEquals($message->id, $recepient_participation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
		$this->assertTrue($message->isViewed());
		$this->assertEquals(0, $recepient_participation->new_messages_count);
		$this->assertEquals(0, $recepient_user->fresh()->getNewMessagesCount());
	}
}