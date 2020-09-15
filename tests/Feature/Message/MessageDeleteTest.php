<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Tests\TestCase;

class MessageDeleteTest extends TestCase
{
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
}
