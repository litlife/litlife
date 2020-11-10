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
		$recepient = User::factory()->create();

		$message = Message::factory()->create(['recepient_id' => $recepient->id]);

		$sender = $message->create_user;

		$senderParticipation = $message->getSenderParticipation();
		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals($senderParticipation->id, $recepientParticipation->id);
		$this->assertEquals($senderParticipation->conversation_id, $recepientParticipation->conversation_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);

		$this->assertFalse($message->isViewed());

		$senderParticipation = $sender->participations()->first();

		$this->assertEquals($message->id, $senderParticipation->latest_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals(0, $sender->getNewMessagesCount());

		$recepientParticipation = $recepient->participations()->first();

		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertNull($recepientParticipation->latest_seen_message_id);
		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(1, $recepient->getNewMessagesCount());
	}

	public function testCreateBB()
	{
		$recepient = User::factory()->create();

		$message = Message::factory()->create([
				'recepient_id' => $recepient->id,
				'bb_text' => 'text https://domain.com/away?=test text'
			]);

		$this->assertEquals('text <a class="bb" href="/away?url=https%3A%2F%2Fdomain.com%2Faway%3F%3Dtest" target="_blank">https://domain.com/away?=test</a> text', $message->text);
	}

	public function testBBEmpty()
	{
		$recepient = User::factory()->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $recepient->id
			]);

		$this->expectException(QueryException::class);

		$message->bb_text = '';
		$message->save();
	}
	/*
		public function test()
		{
			$firstUser = User::factory()->create();

			$secondUser = User::factory()->create();

			$text = Faker::create()->realText(200);

			$this->actingAs($firstUser)
				->post(route('users.messages.store', ['user' => $secondUser]), ['bb_text' => $text])
				->assertSessionHasNoErrors()
				->assertRedirect();

			Carbon::setTestNow(now()->addMinutes(1));

			$message1 = $firstUser->messages()->first();

			$conversation = $message1->conversation()->first();

			$this->actingAs($firstUser)
				->get(route('users.messages.index', ['user' => $secondUser]))
				->assertOK();

			$this->actingAs($secondUser)
				->get(route('users.messages.index', ['user' => $firstUser]))
				->assertOK();

			$this->actingAs($secondUser)
				->post(route('users.messages.store', ['user' => $firstUser]), ['bb_text' => $text])
				->assertSessionHasNoErrors()
				->assertRedirect();

			Carbon::setTestNow(now()->addMinutes(2));

			$this->actingAs($firstUser)
				->get(route('users.messages.index', ['user' => $secondUser]))
				->assertOK();

			$this->actingAs($secondUser)
				->get(route('users.messages.index', ['user' => $firstUser]))
				->assertOK();

			$message2 = $secondUser->messages()->first();

			$response = $this->actingAs($secondUser)
				->delete(route('messages.destroy', $message1->id))
				->assertOk();

			Carbon::setTestNow(now()->addMinutes(3));

			$response = $this->actingAs($secondUser)
				->delete(route('messages.destroy', $message2->id))
				->assertOk();

			Carbon::setTestNow(now()->addMinutes(4));

			$response = $this->actingAs($firstUser)
				->delete(route('messages.destroy', $message2->id))
				->assertOk();

			Carbon::setTestNow(now()->addMinutes(5));

			$response = $this->actingAs($firstUser)
				->delete(route('messages.destroy', $message1->id))
				->assertOk();

			Carbon::setTestNow(now()->addMinutes(6));

			$message1->refresh();
			$message2->refresh();

			dump($message1);
			dump($message2);

			$firstUserParticipation = $conversation->participations()->where('user_id', $firstUser->id)->first();
			$secondUserParticipation = $conversation->participations()->where('user_id', $secondUser->id)->first();

			dump($firstUserParticipation);
			dump($secondUserParticipation);

			$this->assertEquals(0, $firstUserParticipation->new_message_count);
			$this->assertEquals(0, $secondUserParticipation->new_message_count);
		}
	*/
}
