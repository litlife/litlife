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

	public function testStoreHttp()
	{
		$recepient = factory(User::class)->create()->fresh();

		$sender = factory(User::class)->create()->fresh();

		$text = Faker::create()->realText(200);

		$this->actingAs($sender)
			->post(route('users.messages.store', ['user' => $recepient]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.messages.index', ['user' => $recepient]));

		$message = $sender->messages()->first();

		$this->assertEquals($message->text, $text);
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


	public function testDeleteViewedMessageForUser()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->states('viewed')
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$message_to_delete = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $message->create_user_id,
				'create_user_id' => $recepient->id
			])
			->fresh();

		$this->assertEquals(2, $message->conversation->participations()->count());
		$this->assertEquals($message->conversation->id, $message_to_delete->conversation->id);
		$this->assertEquals(2, $message->conversation->messages()->count());

		$this->assertEquals($message->create_user_id, $message_to_delete->recepients_participations()->first()->user_id);
		$this->assertEquals($message_to_delete->create_user_id, $message->recepients_participations()->first()->user_id);

		$conversation = $message->conversation->fresh();

		$message_to_delete->deleteForUser($recepient);

		$this->assertEquals(1, $message_to_delete->user_deletetions()->count());
		$this->assertEquals(1, $conversation->messages()->notDeletedForUser($recepient)->count());
		$this->assertEquals(2, $conversation->messages()->notDeletedForUser($message->create_user)->count());
		$this->assertEquals($message->id, $conversation->messages()->notDeletedForUser($recepient)->first()->id);
		$this->assertEquals($message->id, $conversation->participations()->where('user_id', $recepient->id)->first()->latest_message_id);

		$message_to_delete->refresh();

		$this->assertNotEquals($recepient->id, $message->create_user->id);

		$message_to_delete->deleteForUser($message->create_user);

		$this->assertEquals($message->id, $conversation->messages()->notDeletedForUser($message->create_user)->first()->id);
		$this->assertEquals(2, $message_to_delete->user_deletetions()->count());
		$this->assertEquals(1, $conversation->messages()->notDeletedForUser($recepient)->count());
		$this->assertEquals(1, $conversation->messages()->notDeletedForUser($message->create_user)->count());

		$this->assertEquals($message->id, $conversation->participations()->where('user_id', $message->create_user->id)->first()->latest_message_id);
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

		$this->assertNotNull($message->fresh()->sender_participation()->latest_seen_message_id);
		$this->assertNull($message->fresh()->recepients_participations()->first()->latest_seen_message_id);

		$this->assertNotNull($message2->fresh()->sender_participation()->latest_seen_message_id);
		$this->assertNull($message2->fresh()->recepients_participations()->first()->latest_seen_message_id);

		$this->assertFalse($message->isViewed());
		$this->assertFalse($message2->isViewed());
		$this->assertTrue($message->isNotViewed());
		$this->assertTrue($message2->isNotViewed());
	}

	public function testDeleteLastNotViewedMessage()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message_to_delete = factory(Message::class)
			->create([
				'recepient_id' => $recepient->id
			])
			->fresh();


		$recepient->flushCacheNewMessages();
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$message_to_delete->deleteForUser($message_to_delete->create_user);
		$message_to_delete->refresh();

		$this->assertTrue($message_to_delete->fresh()->trashed());
		$this->assertEquals(0, $message_to_delete->conversation->messages()->notDeletedForUser($message_to_delete->create_user)->count());
		$this->assertEquals(0, $message_to_delete->conversation->messages()->notDeletedForUser($recepient)->count());
		//$this->assertEquals($message_to_delete->id, $message_to_delete->conversation->messages()->notDeletedForUser($recepient)->first());

		$message_to_delete->create_user->flushCacheNewMessages();
		$this->assertEquals(0, $message_to_delete->create_user->getNewMessagesCount());

		$recepient->flushCacheNewMessages();
		$this->assertEquals(0, $recepient->getNewMessagesCount());

		$this->assertEquals(0, $message_to_delete->fresh()->sender_participation()->latest_message_id);
		$this->assertEquals(0, $message_to_delete->fresh()->recepients_participations()->first()->latest_message_id);

		// restore

		$message_to_delete->restoreForUser($message_to_delete->create_user);
		$message_to_delete->refresh();

		$this->assertFalse($message_to_delete->fresh()->trashed());
		$this->assertEquals(1, $message_to_delete->conversation->messages()->count());
		$this->assertEquals(1, $message_to_delete->conversation->messages()->notDeletedForUser($message_to_delete->create_user)->count());
		$this->assertEquals(1, $message_to_delete->conversation->messages()->notDeletedForUser($recepient)->count());

		$recepient->flushCacheNewMessages();
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		$this->assertEquals($message_to_delete->id, $message_to_delete->fresh()->sender_participation()->latest_message_id);
		$this->assertEquals($message_to_delete->id, $message_to_delete->fresh()->recepients_participations()->first()->latest_message_id);
	}

	public function testHttpDeleteMessage()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->states('viewed')
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$response = $this->actingAs($recepient)
			->delete(route('messages.destroy', $message->id))
			->assertOk()
			->assertSee('deleted_at');

		$message = Message::joinUserDeletions($recepient->id)
			->findOrFail($message->id);

		$this->assertNotNull($message->message_deletions_deleted_at);
		$this->assertNotNull($response->decodeResponseJson()['deleted_at']);
	}


	public function testHttpDeleteNotViewedMessage()
	{
		$recepient = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->create(['recepient_id' => $recepient->id])
			->fresh();

		$response = $this->actingAs($recepient)
			->delete(route('messages.destroy', $message->id))
			->assertOk()
			->assertSee('deleted_at');

		$message = Message::withTrashed()
			->findOrFail($message->id);

		$this->assertNotNull($message->deleted_at);
		$this->assertNotNull($response->decodeResponseJson()['deleted_at']);
	}

	public function testDeleteNotViewedMessage()
	{
		$sender_user = factory(User::class)->create()->fresh();
		$recepient_user = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id
			])
			->fresh();

		$message_deleted = factory(Message::class)
			->create([
				'create_user_id' => $sender_user->id,
				'recepient_id' => $recepient_user->id,
				'created_at' => $message->created_at->addSeconds(5)
			])
			->fresh();

		$sender_participation = $message->sender_participation();
		$recepient_participation = $message->recepients_participations()->first();

		$this->assertEquals($message_deleted->id, $sender_participation->latest_message_id);
		$this->assertEquals($message_deleted->id, $sender_participation->latest_seen_message_id);

		$this->assertEquals($message_deleted->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);

		$this->assertEquals($sender_participation->latest_message_id, $recepient_participation->latest_message_id);

		$this->assertEquals($message->conversation->id, $message_deleted->conversation->id);
		/*
				dump('message ' . $message->id);
				dump('message_to_delete ' . $message_deleted->id);
		*/
		$this->assertEquals($message_deleted->id, $message->sender_participation()->latest_message_id);
		$this->assertEquals($message_deleted->id, $message->sender_participation()->latest_seen_message_id);

		$this->assertEquals($message_deleted->id, $message->recepients_participations()->first()->latest_message_id);
		$this->assertNull($message->recepients_participations()->first()->latest_seen_message_id);

		$recepient_user->flushCacheNewMessages();
		$this->assertEquals(2, $recepient_user->getNewMessagesCount());

		$message_deleted->deleteForUser($sender_user);
		$message_deleted->refresh();

		$this->assertTrue($message_deleted->fresh()->trashed());
		$this->assertEquals(1, $message_deleted->conversation->messages()->notDeletedForUser($message_deleted->create_user)->count());
		$this->assertEquals(1, $message_deleted->conversation->messages()->notDeletedForUser($recepient_user)->count());
		$this->assertEquals($message->id, $message_deleted->conversation->messages()->notDeletedForUser($recepient_user)->first()->id);
		//$this->assertEquals($message_deleted->id, $message_deleted->conversation->messages()->notDeletedForUser($recepient)->first());

		$message_deleted->create_user->flushCacheNewMessages();
		$this->assertEquals(0, $message_deleted->create_user->getNewMessagesCount());
		$recepient_user->flushCacheNewMessages();
		$this->assertEquals(1, $recepient_user->getNewMessagesCount());


		$this->assertEquals($message->id, $message->fresh()->sender_participation()->latest_message_id);
		$this->assertEquals($message->id, $message->fresh()->recepients_participations()->first()->latest_message_id);

		$message_deleted->refresh();
		$message->refresh();

		// restore

		$message_deleted->restoreForUser($sender_user);
		$message_deleted->refresh();
		$message->refresh();

		$sender_participation = $message->sender_participation();
		$recepient_participation = $message->recepients_participations()->first();

		$this->assertEquals($message_deleted->id, $sender_participation->latest_message_id);
		$this->assertEquals($message_deleted->id, $sender_participation->latest_seen_message_id);

		$this->assertEquals($message_deleted->id, $recepient_participation->latest_message_id);
		$this->assertNull($recepient_participation->latest_seen_message_id);

		$this->assertEquals($sender_participation->latest_message_id, $recepient_participation->latest_message_id);

		$this->assertFalse($message_deleted->fresh()->trashed());
		$this->assertEquals(2, $message_deleted->conversation->messages()->count());
		$this->assertEquals(2, $message_deleted->conversation->messages()->notDeletedForUser($message_deleted->create_user)->count());
		$this->assertEquals(2, $message_deleted->conversation->messages()->notDeletedForUser($recepient_user)->count());

		$message_deleted->create_user->flushCacheNewMessages();
		$this->assertEquals(0, $message_deleted->create_user->getNewMessagesCount());
		$recepient_user->flushCacheNewMessages();
		$this->assertEquals(2, $recepient_user->getNewMessagesCount());

		/*
				dump($message->sender_participation()->latest_message_id);
				dump($message->recepients_participations()->first()->latest_message_id);
		*/

	}

	public function testEditPermissions()
	{
		$user = factory(User::class)
			->create();

		$user2 = factory(User::class)
			->create();

		$message = factory(Message::class)
			->create(['create_user_id' => $user->id, 'recepient_id' => $user2->id]);

		$this->assertTrue($user->can('update', $message));
		$this->assertFalse($user2->can('update', $message));

		$time = now()->addMinutes(config('litlife.time_that_can_edit_message'))->addMinute();
		Carbon::setTestNow($time);

		$this->assertFalse($user->can('update', $message));
		$this->assertFalse($user2->can('update', $message));
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

	public function testPreview()
	{
		$message = factory(Message::class)
			->create(
				[
					'bb_text' => '[quote]quote[/quote]text[quote]quote[/quote]text',
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('text text', $message->getPreviewText());

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => 'text text',
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('text text', $message->getPreviewText());

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => 'text [img]http://test/image.jpeg[/img]',
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('text ' . '(' . __('message.image') . ')', $message->getPreviewText());

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => '[quote][color=#343a40][font=Arial]quote[/font][/color]' .
						'[url=https://litlife.club/][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url]' .
						'[url=https://litlife.club][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url][/quote]text' .
						'[quote][color=#343a40][font=Arial]quote[/font][/color][/quote]' .
						'text',
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('text text', $message->getPreviewText());

		$s = '[quote][color=#343a40][font=Arial]цитата[/font][/color]
[url=https://litlife.club/][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url]
[url=https://litlife.club/][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url][/quote]
текст
[quote][color=#343a40][font=Arial]цитата[/font][/color][/quote]
текст[quote][color=#343a40]цитата[/color]
[color=#212529]цитата[/color][/quote]
текст';

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => $s,
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('текст текст текст', $message->getPreviewText());


		$s = 'test   test';

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => $s,
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('test   test', $message->getPreviewText());

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => '[img]http://test/image.jpeg[/img]',
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('(' . __('message.image') . ')', $message->getPreviewText());

		$message = factory(Message::class)
			->create(
				[
					'bb_text' => 'текст [youtube]test[/youtube] текст',
					'recepient_id' => factory(User::class)->create()->id
				]
			);

		$this->assertEquals('текст (' . __('message.video') . ') текст', $message->getPreviewText());
	}

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

	public function testInboxPreviewText()
	{
		$auth_user = factory(User::class)->create();
		$user = factory(User::class)->create();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'create_user_id' => $auth_user->id,
				'recepient_id' => $user->id
			])
			->fresh();

		$message2 = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $auth_user->id,
				'create_user_id' => $user->id,
				'bb_text' => '[quote]text2[/quote]text'
			])
			->fresh();

		$this->actingAs($user)
			->get(route('users.inbox', $user))
			->assertOk()
			->assertSeeText(__('common.you') . ': text');
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

	public function testDeleteRestoreOneMessage()
	{
		$admin = factory(User::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($admin)
			->post(route('users.messages.store', ['user' => $user]), [
				'bb_text' => 'text'
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$message = $admin->messages()->first();

		$this->assertNotNull($message);

		$this->actingAs($admin)
			->delete(route('messages.destroy', ['message' => $message]))
			->assertOk();

		$message->refresh();
		$this->assertTrue($message->trashed());

		$this->actingAs($admin)
			->delete(route('messages.destroy', ['message' => $message]))
			->assertOk();

		$message->refresh();
		$this->assertFalse($message->trashed());
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

	public function test401UsersInbox()
	{
		$user = factory(User::class)->create();

		$this->get(route('users.inbox', ['user' => $user]))
			->assertStatus(401);
	}

	public function testNotificationSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$recepient = factory(User::class)->states('with_confirmed_email')->create();
		$recepient->email_notification_setting->private_message = true;
		$recepient->email_notification_setting->save();

		$message = factory(Message::class)
			->create([
				'recepient_id' => $recepient->id
			]);

		Notification::assertSentTo([$recepient], NewPersonalMessageNotification::class);
	}

	public function testNotificationNotSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$recepient = factory(User::class)->states('with_confirmed_email')->create();
		$recepient->email_notification_setting->private_message = false;
		$recepient->email_notification_setting->save();

		$message = factory(Message::class)
			->create([
				'recepient_id' => $recepient->id
			]);

		Notification::assertNotSentTo([$recepient], NewPersonalMessageNotification::class);
	}
}
