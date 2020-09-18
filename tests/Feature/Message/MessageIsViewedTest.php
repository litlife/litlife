<?php

namespace Tests\Feature\Message;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageIsViewedTest extends TestCase
{
	public function testIsViewedFactoryState()
	{
		$recepient = factory(User::class)->create();

		$message = factory(Message::class)
			->states('viewed')
			->create(['recepient_id' => $recepient->id]);

		$this->assertTrue($message->isViewed());
	}

	public function testWithMessageFactoryState()
	{
		$conversation = factory(Conversation::class)
			->states('with_not_viewed_message')
			->create();

		$message = $conversation->messages->first();

		$this->assertFalse($message->isViewed());
	}

	public function testWithViewedMessageFactoryState()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages->first();

		$this->assertTrue($message->isViewed());
	}

	public function testTrueIfRecepientPaticipationHasLatestSeenMessageWithGreaterId()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$participation = $message->getFirstRecepientParticipation();
		$participation->latest_seen_message_id = $message->id + 1;
		$participation->save();

		$this->assertTrue($message->isViewed());
	}

	public function testTrueIfRecepientPaticipationHasLatestSeenMessageWithEqualsId()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$participation = $message->getFirstRecepientParticipation();
		$participation->latest_seen_message_id = $message->id;
		$participation->save();

		$this->assertTrue($message->isViewed());
	}

	public function testFalseIfRecepientPaticipationHasLatestSeenMessageWithLessId()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$participation = $message->getFirstRecepientParticipation();
		$participation->latest_seen_message_id = $message->id - 1;
		$participation->save();

		$this->assertFalse($message->isViewed());
	}

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

		$this->assertNotNull($message->fresh()->getSenderParticipation()->latest_seen_message_id);
		$this->assertNull($message->fresh()->getFirstRecepientParticipation()->latest_seen_message_id);

		$this->assertNotNull($message2->fresh()->getSenderParticipation()->latest_seen_message_id);
		$this->assertNull($message2->fresh()->getFirstRecepientParticipation()->latest_seen_message_id);

		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());
		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());
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

		$this->assertFalse($message->isViewed());

		$sender_participation = $sender_user->participations()->first();

		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(0, $sender_participation->new_messages_count);
		$this->assertEquals(0, $sender_user->fresh()->getNewMessagesCount());
	}
}
