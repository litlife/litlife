<?php

namespace Tests\Feature\Message\Create;

use App\Conversation;
use App\Notifications\NewPersonalMessageNotification;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageStoreTest extends TestCase
{
	public function testStoreHttp()
	{
		Notification::fake();

		$sender = factory(User::class)->create();
		$sender->email_notification_setting->private_message = true;
		$sender->push();

		$recepient = factory(User::class)->states('with_confirmed_email')->create();
		$recepient->email_notification_setting->private_message = true;
		$recepient->push();

		$text = Faker::create()->realText(200);

		$this->actingAs($sender)
			->post(route('users.messages.store', ['user' => $recepient]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.messages.index', ['user' => $recepient]));

		$message = $sender->messages()->first();

		$this->assertEquals($message->text, $text);

		$conversation = $message->conversation;

		$this->assertInstanceOf(Conversation::class, $conversation);

		$senderParticipation = $message->getSenderParticipation();
		$recepientParticipation = $message->getFirstRecepientParticipation();

		$this->assertEquals($conversation->id, $recepientParticipation->conversation->id);
		$this->assertEquals($senderParticipation->conversation->id, $recepientParticipation->conversation->id);

		$this->assertEquals(0, $senderParticipation->new_messages_count);
		$this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $senderParticipation->latest_message_id);

		$this->assertEquals(1, $recepientParticipation->new_messages_count);
		$this->assertEquals(null, $recepientParticipation->latest_seen_message_id);
		$this->assertEquals($message->id, $recepientParticipation->latest_message_id);

		$sender->flushCacheNewMessages();
		$recepient->flushCacheNewMessages();

		$this->assertEquals(0, $sender->getNewMessagesCount());
		$this->assertEquals(1, $recepient->getNewMessagesCount());

		Notification::assertNotSentTo($sender, NewPersonalMessageNotification::class);
		Notification::assertSentTo($recepient, NewPersonalMessageNotification::class);
	}
}
