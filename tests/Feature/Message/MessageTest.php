<?php

namespace Tests\Feature\Message;

use App\Message;
use App\Notifications\NewPersonalMessageNotification;
use App\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
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

	public function testCreate()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$recepient = factory(User::class)->states('with_confirmed_email')->create();

		$text = Faker::create()->text;

		$message = factory(Message::class)
			->create([
				'recepient_id' => $recepient->id,
				'text' => $text
			])
			->fresh();

		$sender = $message->create_user;

		$sender_participation = $sender->participations->first();
		$recepient_participation = $recepient->participations->first();

		$this->assertEquals($sender_participation->id, $recepient_participation->id);
		$this->assertEquals($sender_participation->conversation_id, $recepient_participation->conversation_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);

		$sender->flushCacheNewMessages();
		$recepient->flushCacheNewMessages();

		$this->assertEquals(0, $sender->getNewMessagesCount());
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		Notification::assertSentTo([$recepient], NewPersonalMessageNotification::class);
		Notification::assertNotSentTo([$sender], NewPersonalMessageNotification::class);
	}

	public function testCreateMessageThroughFactory()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$sender = $message->create_user;

		$sender_participation = $message->sender_participation();
		$recepient_participation = $message->recepients_participations()->first();

		$this->assertEquals($sender_participation->id, $recepient_participation->id);
		$this->assertEquals($sender_participation->conversation_id, $recepient_participation->conversation_id);
		$this->assertEquals($message->id, $sender_participation->latest_seen_message_id);
		$this->assertEquals(1, $recepient_participation->new_messages_count);
		$this->assertEquals($message->id, $sender_participation->latest_message_id);
		$this->assertEquals($message->id, $recepient_participation->latest_message_id);
	}

	public function testLatestParticipationsForHourCount()
	{
		$user = factory(User::class)
			->create();

		$user1 = factory(User::class)->create();

		$message1 = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user1->id
			])->fresh();

		$this->assertEquals(1, $user->latest_new_particaipations_for_hour_count());

		$user2 = factory(User::class)->create();

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user2->id
			])->fresh();

		$this->assertEquals(2, $user->latest_new_particaipations_for_hour_count());

		$user3 = factory(User::class)->create();

		$message3 = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user3->id
			])->fresh();

		$this->assertEquals(3, $user->latest_new_particaipations_for_hour_count());

		$message4 = factory(Message::class)
			->create([
				'create_user_id' => $user3->id,
				'recepient_id' => $user->id
			])->fresh();

		$this->assertEquals(2, $user->latest_new_particaipations_for_hour_count());

		$message2->deleteForUser($user);

		$this->assertEquals(1, $user->latest_new_particaipations_for_hour_count());

		$time = now()->addHour()->addMinute();

		Carbon::setTestNow($time);

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		//

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user1
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user2
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user3
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());
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
			])
			->fresh();

		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());

		$this->actingAs($user)
			->get(route('users.inbox', $user))
			->assertOk()
			->assertSeeText(__('user.deleted'));

		$this->actingAs($user)
			->get(route('users.messages.index', $deleted_user))
			->assertOk()
			->assertSeeText(__('user.deleted'))
			->assertSeeText($message->text);

		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());

		$participation = $user->participations()->first();
		$this->assertEquals(0, $participation->new_messages_count);

		$this->actingAs($user)
			->delete(route('messages.destroy', $message))
			->assertOk();

		$participation = $user->participations()->first();

		$this->assertNull($participation->latest_message_id);
		$this->assertEquals($message->id, $participation->latest_seen_message_id);
		$this->assertEquals(0, $participation->new_messages_count);

		$this->assertEquals(0, $user->fresh()->getNewMessagesCount());
	}
}
