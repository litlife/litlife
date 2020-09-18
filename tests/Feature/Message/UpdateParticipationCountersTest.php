<?php

namespace Tests\Feature\Message;

use App\Jobs\UpdateParticipationCounters;
use App\Message;
use App\MessageDelete;
use App\Participation;
use App\User;
use Tests\TestCase;

class UpdateParticipationCountersTest extends TestCase
{
	public function testCountersAfterSpamer()
	{
		$auth_user = factory(User::class)
			->create();

		$spamer = factory(User::class)->create();

		$text = uniqid() . uniqid();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $spamer->id,
				'recepient_id' => $auth_user->id,
				'bb_text' => $text
			])->fresh();

		$conversation = $message->conversation;
		$auth_user_participation = $message->getFirstRecepientParticipation();
		$spamer_participation = $message->getSenderParticipation();

		$this->assertNotNull($conversation);
		$this->assertNotNull($auth_user_participation);
		$this->assertNotNull($spamer_participation);

		Participation::where('user_id', $spamer->id)
			->where('conversation_id', $conversation->id)
			->update(['latest_seen_message_id' => 0]);

		MessageDelete::create([
			'message_id' => $message->id,
			'user_id' => $auth_user->id,
			'deleted_at' => now()
		]);

		$this->actingAs($auth_user)
			->get(route('users.inbox', $auth_user))
			->assertSee($text);

		UpdateParticipationCounters::dispatch($auth_user_participation);
		UpdateParticipationCounters::dispatch($spamer_participation);

		$message->fresh();

		$auth_user_participation = $message->getFirstRecepientParticipation();
		$spamer_participation = $message->getSenderParticipation();

		$this->assertEmpty($auth_user_participation->latest_message_id);

		$this->actingAs($auth_user)
			->get(route('users.inbox', $auth_user))
			->assertDontSee($text)
			->assertDontSee($spamer->name);
	}

	public function testNotViewed()
	{
		$auth_user = factory(User::class)
			->create();

		$user = factory(User::class)->create();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $auth_user->id,
			])->fresh();

		UpdateParticipationCounters::dispatch($message->getFirstRecepientParticipation());
		UpdateParticipationCounters::dispatch($message->getSenderParticipation());

		$message->refresh();

		$this->assertEquals(1, $message->getFirstRecepientParticipation()->new_messages_count);
		$this->assertEquals(0, $message->getSenderParticipation()->new_messages_count);

		$this->assertEquals($message->id, $message->getFirstRecepientParticipation()->latest_message_id);
		$this->assertEquals($message->id, $message->getSenderParticipation()->latest_message_id);

		$this->assertEquals(0, $message->getFirstRecepientParticipation()->latest_seen_message_id);
		$this->assertEquals($message->id, $message->getSenderParticipation()->latest_seen_message_id);
	}

	public function testViewed()
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
			->states('viewed')
			->create([
				'create_user_id' => $auth_user->id,
				'recepient_id' => $user->id
			])
			->fresh();

		UpdateParticipationCounters::dispatch($message->getFirstRecepientParticipation());
		UpdateParticipationCounters::dispatch($message->getSenderParticipation());

		$message->refresh();

		$this->assertEquals(0, $message->getFirstRecepientParticipation()->new_messages_count);
		$this->assertEquals(0, $message->getSenderParticipation()->new_messages_count);

		$this->assertEquals($message2->id, $message->getFirstRecepientParticipation()->latest_message_id);
		$this->assertEquals($message2->id, $message->getSenderParticipation()->latest_message_id);

		$this->assertEquals($message2->id, $message->getFirstRecepientParticipation()->latest_seen_message_id);
		$this->assertEquals($message2->id, $message->getSenderParticipation()->latest_seen_message_id);
	}

	public function testFixBugPost726240()
	{
		// https://litlife.club/posts/726240/go_to

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

		$recepient_participation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepient_participation->new_messages_count);

		// delete
		$this->actingAs($recepient_user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$recepient_participation->new_messages_count = 2;
		$recepient_participation->latest_seen_message_id = null;
		$recepient_participation->latest_message_id = null;
		$recepient_participation->save();

		UpdateParticipationCounters::dispatch($recepient_participation);

		$recepient_participation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepient_participation->new_messages_count);
	}
}