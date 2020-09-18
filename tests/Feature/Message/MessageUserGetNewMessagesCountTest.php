<?php

namespace Tests\Feature\Message;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageUserGetNewMessagesCountTest extends TestCase
{
	public function testNotViewedMessageUserParticipation()
	{
		$conversation = factory(Conversation::class)
			->states('with_not_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();
		$recepient = $recepientParticipation->user;

		$this->assertEquals(1, $recepient->getNewMessagesCount());
		$this->assertEquals(0, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
	}

	public function testCountNew()
	{
		$recepient = factory(User::class)
			->create();

		$message_viewed = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $recepient->id
			]);

		$this->assertEquals($message_viewed->id, $message_viewed->getFirstRecepientParticipation()->latest_seen_message_id);
		$this->assertEquals($message_viewed->id, $message_viewed->getFirstRecepientParticipation()->latest_message_id);
		$this->assertEquals(0, $message_viewed->getFirstRecepientParticipation()->new_messages_count);

		$message = factory(Message::class)
			->create([
				'create_user_id' => $message_viewed->create_user_id,
				'recepient_id' => $recepient->id
			]);

		$message_viewed->refresh();
		$message->refresh();

		$this->assertEquals($message_viewed->id, $message->getFirstRecepientParticipation()->latest_seen_message_id);
		$this->assertEquals($message->id, $message->getFirstRecepientParticipation()->latest_message_id);
		$this->assertEquals(1, $message->getFirstRecepientParticipation()->new_messages_count);

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $message_viewed->create_user_id,
				'recepient_id' => $recepient->id
			]);

		$message->refresh();
		$message2->refresh();

		$this->assertEquals($message_viewed->id, $message->getFirstRecepientParticipation()->latest_seen_message_id);
		$this->assertEquals($message2->id, $message->getFirstRecepientParticipation()->latest_message_id);
		$this->assertEquals(2, $message->getFirstRecepientParticipation()->new_messages_count);


		$message3 = factory(Message::class)
			->create([
				'create_user_id' => $message_viewed->create_user_id,
				'recepient_id' => $recepient->id
			]);

		$message->refresh();
		$message3->refresh();

		$recepient->flushCacheNewMessages();

		$this->assertEquals($message_viewed->id, $message->getFirstRecepientParticipation()->latest_seen_message_id);
		$this->assertEquals($message3->id, $message->getFirstRecepientParticipation()->latest_message_id);
		$this->assertEquals(3, $recepient->getNewMessagesCount());
	}
}
