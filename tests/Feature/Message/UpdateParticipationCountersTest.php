<?php

namespace Tests\Feature\Message;

use App\Message;
use App\MessageDelete;
use App\Participation;
use App\User;
use Tests\TestCase;

class UpdateParticipationCountersTest extends TestCase
{
    public function testCountersAfterSpamer()
    {
        $auth_user = User::factory()->create();

        $spamer = User::factory()->create();

        $text = uniqid().uniqid();

        $message = Message::factory()->create([
            'create_user_id' => $spamer->id,
            'recepient_id' => $auth_user->id,
            'bb_text' => $text
        ]);

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

        $auth_user_participation = $message->getFirstRecepientParticipation();
        $spamer_participation = $message->getSenderParticipation();

        $auth_user_participation->updateNewMessagesCount();
        $auth_user_participation->updateLatestMessage();
        $auth_user_participation->save();

        $spamer_participation->updateNewMessagesCount();
        $spamer_participation->updateLatestMessage();
        $spamer_participation->save();

        $message->refresh();

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
        $auth_user = User::factory()->create();

        $user = User::factory()->create();

        $message = Message::factory()->create([
            'create_user_id' => $user->id,
            'recepient_id' => $auth_user->id,
        ]);

        $this->assertEquals(1, $message->getFirstRecepientParticipation()->new_messages_count);
        $this->assertEquals(0, $message->getSenderParticipation()->new_messages_count);

        $this->assertEquals($message->id, $message->getFirstRecepientParticipation()->latest_message_id);
        $this->assertEquals($message->id, $message->getSenderParticipation()->latest_message_id);

        $this->assertEquals(0, $message->getFirstRecepientParticipation()->latest_seen_message_id);
        $this->assertEquals($message->id, $message->getSenderParticipation()->latest_seen_message_id);
    }

    public function testViewed()
    {
        $auth_user = User::factory()->create();

        $user = User::factory()->create();

        $message = Message::factory()
            ->viewed()
            ->create([
                'create_user_id' => $user->id,
                'recepient_id' => $auth_user->id
            ]);

        $message2 = Message::factory()
            ->viewed()
            ->create([
                'create_user_id' => $auth_user->id,
                'recepient_id' => $user->id
            ]);

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

        $sender = User::factory()->create();

        $recepient = User::factory()->create();

        $message = Message::factory()
            ->viewed()
            ->create([
                'create_user_id' => $sender->id,
                'recepient_id' => $recepient->id
            ]);

        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertEquals(0, $recepientParticipation->new_messages_count);

        // delete
        $this->actingAs($recepient)
            ->delete(route('messages.destroy', $message))
            ->assertOk();

        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertEquals(0, $recepientParticipation->new_messages_count);
    }
}