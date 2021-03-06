<?php

namespace Tests\Feature\Message;

use App\Conversation;
use App\Message;
use App\User;
use Tests\TestCase;

class MessageIsViewedTest extends TestCase
{
    public function testIsViewedFactoryState()
    {
        $recepient = User::factory()->create();

        $message = Message::factory()->viewed()->create(['recepient_id' => $recepient->id]);

        $this->assertTrue($message->isViewed());
    }

    public function testWithMessageFactoryState()
    {
        $conversation = Conversation::factory()->with_not_viewed_message()->create();

        $message = $conversation->messages->first();

        $this->assertFalse($message->isViewed());
    }

    public function testWithViewedMessageFactoryState()
    {
        $conversation = Conversation::factory()->with_viewed_message()->create();

        $message = $conversation->messages->first();

        $this->assertTrue($message->isViewed());
    }

    public function testTrueIfRecepientPaticipationHasLatestSeenMessageWithGreaterId()
    {
        $conversation = Conversation::factory()->with_viewed_message()->create();

        $message = $conversation->messages()->first();

        $participation = $message->getFirstRecepientParticipation();
        $participation->latest_seen_message_id = $message->id + 1;
        $participation->save();

        $this->assertTrue($message->isViewed());
    }

    public function testTrueIfRecepientPaticipationHasLatestSeenMessageWithEqualsId()
    {
        $conversation = Conversation::factory()->with_viewed_message()->create();

        $message = $conversation->messages()->first();

        $participation = $message->getFirstRecepientParticipation();
        $participation->latest_seen_message_id = $message->id;
        $participation->save();

        $this->assertTrue($message->isViewed());
    }

    public function testFalseIfRecepientPaticipationHasLatestSeenMessageWithLessId()
    {
        $conversation = Conversation::factory()->with_viewed_message()->create();

        $message = $conversation->messages()->first();

        $participation = $message->getFirstRecepientParticipation();
        $participation->latest_seen_message_id = $message->id - 1;
        $participation->save();

        $this->assertFalse($message->isViewed());
    }

    public function testViewed()
    {
        $recepient = User::factory()->create();

        $message = Message::factory()
            ->viewed()
            ->create(['recepient_id' => $recepient->id]);

        $message2 = Message::factory()
            ->viewed()
            ->create([
                'recepient_id' => $message->create_user_id,
                'create_user_id' => $recepient->id
            ]);

        $this->assertTrue($message->isViewed());
        $this->assertTrue($message2->isViewed());

        // two viewed messages from one user

        $recepient = User::factory()->create();

        $message = Message::factory()->viewed()->create(['recepient_id' => $recepient->id]);

        $message2 = Message::factory()->viewed()
            ->create([
                'create_user_id' => $message->create_user_id,
                'recepient_id' => $recepient->id
            ]);

        $this->assertTrue($message->isViewed());
        $this->assertTrue($message2->isViewed());


        // // two not viewed messages from one user

        $recepient = User::factory()->create();

        //dump("recepient id: $recepient->id");

        $message = Message::factory()->create(['recepient_id' => $recepient->id]);

        $message2 = Message::factory()->create([
            'create_user_id' => $message->create_user_id,
            'recepient_id' => $recepient->id
        ]);

        //dump("message id: $message->id");
        //dump("message2 id: $message2->id");

        $this->assertNotNull($message->getSenderParticipation()->latest_seen_message_id);
        $this->assertNull($message->getFirstRecepientParticipation()->latest_seen_message_id);

        $this->assertNotNull($message2->getSenderParticipation()->latest_seen_message_id);
        $this->assertNull($message2->getFirstRecepientParticipation()->latest_seen_message_id);

        $this->assertFalse($message->isViewed());
        $this->assertFalse($message2->isViewed());
        $this->assertFalse($message->isViewed());
        $this->assertFalse($message2->isViewed());
    }

    public function testNotViewedShouldBeLastViewedToSender()
    {
        $sender = User::factory()->create();

        $recepient = User::factory()->create();

        $message = Message::factory()->create([
            'create_user_id' => $sender->id,
            'recepient_id' => $recepient->id
        ]);

        $this->assertFalse($message->isViewed());

        $senderParticipation = $sender->participations()->first();
        $sender->refresh();

        $this->assertEquals($message->id, $senderParticipation->latest_message_id);
        $this->assertEquals($message->id, $senderParticipation->latest_seen_message_id);
        $this->assertEquals(0, $senderParticipation->new_messages_count);
        $this->assertEquals(0, $sender->getNewMessagesCount());
    }
}
