<?php

namespace Tests\Feature\Message\Restore;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageRestoreForRecepientTest extends TestCase
{
    public function testIfMessageNotViewed()
    {
        $conversation = Conversation::factory()->with_two_not_viewed_message()->create();

        $firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
        $secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $sender = $secondMessage->create_user;
        $recepient = $recepientParticipation->user;

        $secondMessage->deleteForUser($recepient);
        $secondMessage->refresh();
        $secondMessage->restoreForUser($recepient);
        $secondMessage->refresh();

        $this->assertFalse($secondMessage->trashed());

        $secondMessage->refresh();

        $this->assertTrue($secondMessage->isViewed());

        $recepientParticipation = $secondMessage->getFirstRecepientParticipation();

        $this->assertEquals(0, $recepientParticipation->new_messages_count);
        $this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
        $this->assertEquals($secondMessage->id, $recepientParticipation->latest_message_id);
        $this->assertEquals(0, $recepient->getNewMessagesCount());

        $senderParticipation = $secondMessage->getSenderParticipation();

        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals($secondMessage->id, $senderParticipation->latest_message_id);
        $this->assertEquals(0, $sender->getNewMessagesCount());
    }

    public function testIfMessageViewed()
    {
        $conversation = Conversation::factory()->with_two_viewed_message()->create();

        $firstMessage = $conversation->messages()->orderBy('id', 'asc')->first();
        $secondMessage = $conversation->messages()->orderBy('id', 'desc')->first();

        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $sender = $secondMessage->create_user;
        $recepient = $recepientParticipation->user;

        $secondMessage->deleteForUser($recepient);
        $secondMessage->refresh();
        $secondMessage->restoreForUser($recepient);
        $secondMessage->refresh();

        $userDeletition = $secondMessage->user_deletetions()->first();

        $this->assertNull($userDeletition);

        $firstMessage->refresh();

        $this->assertTrue($secondMessage->isViewed());

        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $this->assertEquals(0, $recepientParticipation->new_messages_count);
        $this->assertEquals($secondMessage->id, $recepientParticipation->latest_seen_message_id);
        $this->assertEquals($secondMessage->id, $recepientParticipation->latest_message_id);
        $this->assertEquals(0, $recepient->getNewMessagesCount());

        $senderParticipation = $firstMessage->getSenderParticipation();

        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals($secondMessage->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals($secondMessage->id, $senderParticipation->latest_message_id);
        $this->assertEquals(0, $sender->getNewMessagesCount());
    }

    public function testIfNotViewedAndLatest()
    {
        $conversation = Conversation::factory()->with_not_viewed_message()->create();

        $message = $conversation->messages()->first();

        $recepientParticipation = $message->getFirstRecepientParticipation();

        $sender = $message->create_user;
        $recepient = $recepientParticipation->user;

        $message->deleteForUser($recepient);
        $message->refresh();

        $message->restoreForUser($recepient);
        $message->refresh();

        $this->assertFalse($message->trashed());
        $this->assertTrue($message->isViewed());

        $message->refresh();

        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertEquals(0, $recepientParticipation->new_messages_count);
        $this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
        $this->assertEquals($message->id, $recepientParticipation->latest_message_id);
        $this->assertEquals(0, $recepient->getNewMessagesCount());

        $senderParticipation = $message->getSenderParticipation();

        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals($message->id, $senderParticipation->latest_message_id);
        $this->assertEquals(0, $sender->getNewMessagesCount());
    }

    public function testIfViewedAndLatest()
    {
        $conversation = Conversation::factory()->with_viewed_message()->create();

        $message = $conversation->messages()->first();

        $recepientParticipation = $message->getFirstRecepientParticipation();

        $sender = $message->create_user;
        $recepient = $recepientParticipation->user;

        $message->deleteForUser($recepient);
        $message->refresh();
        $message->restoreForUser($recepient);
        $message->refresh();

        $this->assertTrue($message->isViewed());

        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertEquals(0, $recepientParticipation->new_messages_count);
        $this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
        $this->assertEquals($message->id, $recepientParticipation->latest_message_id);
        $this->assertEquals(0, $recepient->getNewMessagesCount());

        $senderParticipation = $message->getSenderParticipation();

        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals($message->id, $senderParticipation->latest_message_id);
        $this->assertEquals(0, $sender->getNewMessagesCount());
    }

    public function testRestoreNotViewed()
    {
        $sender = User::factory()->create();

        $recepient = User::factory()->create();

        $message = Message::factory()->create([
            'create_user_id' => $sender->id,
            'recepient_id' => $recepient->id
        ]);

        // delete
        $this->actingAs($recepient)
            ->delete(route('messages.destroy', $message))
            ->assertOk();

        $message->refresh();

        // restore
        $this->actingAs($recepient)
            ->delete(route('messages.destroy', $message))
            ->assertOk();

        $message->refresh();

        $this->assertTrue($message->isViewed());

        $senderParticipation = $sender->participations()->first();

        $this->assertEquals($message->id, $senderParticipation->latest_message_id);
        $this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals(0, $sender->getNewMessagesCount());

        $recepientParticipation = $recepient->participations()->first();

        $this->assertEquals($message->id, $recepientParticipation->latest_message_id);
        $this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
        $this->assertEquals(0, $recepientParticipation->new_messages_count);
        $this->assertEquals(0, $recepient->getNewMessagesCount());
    }

    public function testRestoreViewed()
    {
        $sender = User::factory()->create();

        $recepient = User::factory()->create();

        $message = Message::factory()
            ->viewed()
            ->create([
                'create_user_id' => $sender->id,
                'recepient_id' => $recepient->id
            ]);

        // delete
        $this->actingAs($recepient)
            ->delete(route('messages.destroy', $message))
            ->assertOk();

        // restore
        $this->actingAs($recepient)
            ->delete(route('messages.destroy', $message))
            ->assertOk();

        $senderParticipation = $sender->participations()->first();

        $this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals($message->id, $senderParticipation->latest_message_id);
        $this->assertTrue($message->isViewed());
        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals(0, $sender->getNewMessagesCount());

        $recepientParticipation = $recepient->participations()->first();

        $this->assertEquals($message->id, $recepientParticipation->latest_seen_message_id);
        $this->assertEquals($message->id, $recepientParticipation->latest_message_id);
        $this->assertTrue($message->isViewed());
        $this->assertEquals(0, $recepientParticipation->new_messages_count);
        $this->assertEquals(0, $recepient->getNewMessagesCount());
    }
}
