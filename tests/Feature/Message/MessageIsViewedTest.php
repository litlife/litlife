<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageIsViewedTest extends TestCase
{

	public function testViewed()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->states('viewed')
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$message2 = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $message->create_user_id,
				'create_user_id' => $recepient->id
			])
			->fresh();

		$this->assertTrue($message->isViewed());
		$this->assertTrue($message2->isViewed());

		// two viewed messages from one user

		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->states('viewed')
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$message2 = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $message->create_user_id,
				'recepient_id' => $recepient->id
			])
			->fresh();

		$this->assertTrue($message->isViewed());
		$this->assertTrue($message2->isViewed());


		// // two not viewed messages from one user

		$recepient = factory(User::class)->create()->fresh();

		//dump("recepient id: $recepient->id");

		$message = factory(Message::class)
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $message->create_user_id,
				'recepient_id' => $recepient->id
			])
			->fresh();

		//dump("message id: $message->id");
		//dump("message2 id: $message2->id");

		$this->assertNotNull($message->fresh()->sender_participation()->latest_seen_message_id);
		$this->assertNull($message->fresh()->recepients_participations()->first()->latest_seen_message_id);

		$this->assertNotNull($message2->fresh()->sender_participation()->latest_seen_message_id);
		$this->assertNull($message2->fresh()->recepients_participations()->first()->latest_seen_message_id);

		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());
		$this->assertTrue($message->isNotViewed());
		$this->assertTrue($message2->isNotViewed());
	}

	public function testNotViewedShouldBeLastViewedToSender()
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
	}
}
