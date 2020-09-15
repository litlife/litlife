<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageUserGetNewMessagesCountTest extends TestCase
{
	public function testView()
	{
		$iam = factory(User::class)->create();
		$user = factory(User::class)->create();

		$text = $this->faker->realText(100);

		$this->followingRedirects()
			->actingAs($user)
			->post(route('users.messages.store', ['user' => $iam]),
				['bb_text' => $text])
			->assertOk()
			->assertSeeText($text);

		$text = $this->faker->realText(100);

		$this->followingRedirects()
			->actingAs($iam)
			->post(route('users.messages.store', ['user' => $user]),
				['bb_text' => $text])
			->assertOk()
			->assertSeeText($text);

		$this->actingAs($iam)
			->get(route('users.messages.index', $user))
			->assertOk();

		$this->actingAs($user)
			->get(route('users.messages.index', $iam))
			->assertOk();

		$this->assertEquals(0, $iam->fresh()->getNewMessagesCount());
		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());

		$text = $this->faker->realText(100);

		$this->followingRedirects()
			->actingAs($user)
			->post(route('users.messages.store', $iam),
				['bb_text' => $text])
			->assertOk()
			->assertSeeText($text);

		$text = $this->faker->realText(100);

		$this->followingRedirects()
			->actingAs($user)
			->post(route('users.messages.store', $iam),
				['bb_text' => $text])
			->assertOk()
			->assertSeeText($text);

		$this->assertEquals(2, $iam->fresh()->getNewMessagesCount());
		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());

		$this->actingAs($iam)
			->get(route('users.messages.index', $user))
			->assertOk();

		$this->assertEquals(0, $iam->fresh()->getNewMessagesCount());
	}

	public function testViewCounterBug()
	{
		$iam = factory(User::class)->create();
		$user = factory(User::class)->create();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('users.messages.store', $iam),
				['bb_text' => $text])
			->assertRedirect();

		$this->assertEquals(1, $iam->fresh()->getNewMessagesCount());
		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());

		$my_participation = $iam->participations()->first();
		$user_participation = $user->participations()->first();

		$this->assertNotNull($my_participation);
		$this->assertNotNull($user_participation);

		$text2 = $this->faker->realText(100);

		$this->actingAs($iam)
			->post(route('users.messages.store', $user),
				['bb_text' => $text2])
			->assertRedirect();
		/*
				$this->assertEquals($my_participation->latest_seen_message_id,
					$iam->fresh()->participations()->first()->latest_seen_message_id);
		*/
		$this->actingAs($iam)
			->get(route('users.messages.index', $user))
			->assertOk()
			->assertSeeText($text)
			->assertSeeText($text2)
			->assertDontSeeText(__('message.new_messages'));

		$this->assertEquals(0, $iam->fresh()->getNewMessagesCount());
		$this->assertEquals(1, $user->fresh()->getNewMessagesCount());

		$this->actingAs($user)
			->get(route('users.messages.index', $iam))
			->assertOk()
			->assertSeeText($text)
			->assertSeeText($text2)
			->assertSeeText(__('message.new_messages'));

		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());
	}

	public function testCountNew()
	{
		$recepient = factory(User::class)
			->create()
			->fresh();

		$message_viewed = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $recepient->id
			])
			->fresh();

		$this->assertEquals($message_viewed->id, $message_viewed->recepients_participations()->first()->latest_seen_message_id);
		$this->assertEquals($message_viewed->id, $message_viewed->recepients_participations()->first()->latest_message_id);
		$this->assertEquals(0, $message_viewed->recepients_participations()->first()->new_messages_count);

		$message = factory(Message::class)
			->create([
				'create_user_id' => $message_viewed->create_user_id,
				'recepient_id' => $recepient->id
			])
			->fresh();

		$message_viewed->refresh();
		$message->refresh();

		$this->assertEquals($message_viewed->id, $message->recepients_participations()->first()->latest_seen_message_id);
		$this->assertEquals($message->id, $message->recepients_participations()->first()->latest_message_id);
		$this->assertEquals(1, $message->recepients_participations()->first()->new_messages_count);

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $message_viewed->create_user_id,
				'recepient_id' => $recepient->id
			])
			->fresh();

		$message->refresh();
		$message2->refresh();

		$this->assertEquals($message_viewed->id, $message->recepients_participations()->first()->latest_seen_message_id);
		$this->assertEquals($message2->id, $message->recepients_participations()->first()->latest_message_id);
		$this->assertEquals(2, $message->recepients_participations()->first()->new_messages_count);


		$message3 = factory(Message::class)
			->create([
				'create_user_id' => $message_viewed->create_user_id,
				'recepient_id' => $recepient->id
			])
			->fresh();

		$message->refresh();
		$message3->refresh();

		$recepient->flushCacheNewMessages();

		$this->assertEquals($message_viewed->id, $message->recepients_participations()->first()->latest_seen_message_id);
		$this->assertEquals($message3->id, $message->recepients_participations()->first()->latest_message_id);
		$this->assertEquals(3, $recepient->fresh()->getNewMessagesCount());
	}
}
