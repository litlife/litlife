<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class MessageTest extends TestCase
{
	public function testFactory()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$sender = $message->create_user;

		$sender_participation = $message->getSenderParticipation();
		$recepient_participation = $message->getFirstRecepientParticipation();

		$this->assertEquals($sender_participation->id, $recepient_participation->id);
		$this->assertEquals($sender_participation->conversation_id, $recepient_participation->conversation_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
	}

	public function testCreateBB()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->create([
				'recepient_id' => $recepient->id,
				'bb_text' => 'text https://domain.com/away?=test text'
			]);

		$this->assertEquals('text <a class="bb" href="/away?url=https%3A%2F%2Fdomain.com%2Faway%3F%3Dtest" target="_blank">https://domain.com/away?=test</a> text', $message->text);
	}

	public function testBBEmpty()
	{
		$recepient = factory(User::class)->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $recepient->id
			]);

		$this->expectException(QueryException::class);

		$message->bb_text = '';
		$message->save();
	}
}
