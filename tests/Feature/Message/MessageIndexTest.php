<?php

namespace Tests\Feature\Message;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageIndexTest extends TestCase
{
	public function testRedirectIfUserMatchAuthUser()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.messages.index', ['user' => $user]))
			->assertRedirect(route('users.inbox', ['user' => $user->id]));
	}

	public function testEmpty()
	{
		$user = factory(User::class)
			->create();

		$user2 = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.messages.index', ['user' => $user2]))
			->assertOk()
			->assertViewHas('user', $user2)
			->assertViewHas('messages', null)
			->assertViewHas('conversation', null)
			->assertViewHas('participation', null);
	}

	public function testIfUserDeletedSeeUserViewMessageAndDelete()
	{
		$user = factory(User::class)
			->create();

		$deleted_user = factory(User::class)
			->create();

		$deleted_user->delete();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $deleted_user->id,
				'recepient_id' => $user->id
			]);

		$this->assertEquals(0, $user->getNewMessagesCount());

		$this->actingAs($user)
			->get(route('users.inbox', $user))
			->assertOk()
			->assertSeeText(__('user.deleted'));

		$this->actingAs($user)
			->get(route('users.messages.index', $deleted_user))
			->assertOk()
			->assertSeeText(__('user.deleted'))
			->assertSeeText($message->text);

		$this->assertEquals(0, $user->getNewMessagesCount());

		$participation = $user->participations()->first();
		$this->assertEquals(0, $participation->new_messages_count);

		$this->actingAs($user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$participation = $user->participations()->first();

		$this->assertNull($participation->latest_message_id);
		$this->assertEquals($message->id, $participation->latest_seen_message_id);
		$this->assertEquals(0, $participation->new_messages_count);

		$this->assertEquals(0, $user->getNewMessagesCount());
	}

	public function testMessagesViewed()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_and_not_viewed_message')
			->create();

		$message = $conversation->messages()
			->latestWithId()
			->first();

		$recepientParticipation = $message->getFirstRecepientParticipation();
		$recepient = $recepientParticipation->user;
		$sender = $message->create_user;

		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$this->actingAs($recepient)
			->get(route('users.messages.index', $sender))
			->assertOk()
			->assertSeeText(__('message.new_messages'))
			->assertViewHas('user', $sender)
			->assertViewHas('conversation', $conversation);

		$message->refresh();

		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals(0, $recepientParticipation->new_messages_count);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
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

		$this->assertEquals(1, $iam->getNewMessagesCount());
		$this->assertEquals(0, $user->getNewMessagesCount());

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
					$iam->participations()->first()->latest_seen_message_id);
		*/
		$this->actingAs($iam)
			->get(route('users.messages.index', $user))
			->assertOk()
			->assertSeeText($text)
			->assertSeeText($text2)
			->assertDontSeeText(__('message.new_messages'));

		$this->assertEquals(0, $iam->getNewMessagesCount());
		$this->assertEquals(1, $user->getNewMessagesCount());

		$this->actingAs($user)
			->get(route('users.messages.index', $iam))
			->assertOk()
			->assertSeeText($text)
			->assertSeeText($text2)
			->assertSeeText(__('message.new_messages'));

		$this->assertEquals(0, $user->getNewMessagesCount());
	}

}
