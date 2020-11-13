<?php

namespace Tests\Feature\Message;

use App\Conversation;
use Tests\TestCase;

class ParticipationGetLatestMessageTest extends TestCase
{
    public function testIfMessageViewed()
    {
        $conversation = Conversation::factory()->with_two_viewed_message()->create();

        $message = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $message->getSenderParticipation();
        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertTrue($message->is($senderParticipation->getLatestMessage()));
        $this->assertTrue($message->is($recepientParticipation->getLatestMessage()));
    }

    public function testIfMessageNotViewed()
    {
        $conversation = Conversation::factory()->with_two_not_viewed_message()->create();

        $message = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $message->getSenderParticipation();
        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertTrue($message->is($senderParticipation->getLatestMessage()));
        $this->assertTrue($message->is($recepientParticipation->getLatestMessage()));
    }

    public function testIfLatestViewedMessageDeletedForSender()
    {
        $conversation = Conversation::factory()->with_two_viewed_message()->create();

        $firstMessage = $conversation->messages()->oldestWithId()->first();
        $secondMessage = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $firstMessage->getSenderParticipation();
        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $sender = $senderParticipation->user;
        $recepient = $recepientParticipation->user;

        $secondMessage->user_deletetions()
            ->firstOrCreate(
                ['user_id' => $sender->id],
                ['deleted_at' => now()]
            );

        $this->assertTrue($firstMessage->is($senderParticipation->getLatestMessage()));
        $this->assertTrue($secondMessage->is($recepientParticipation->getLatestMessage()));
    }

    public function testIfLatestViewedMessageDeletedForRecepient()
    {
        $conversation = Conversation::factory()->with_two_viewed_message()->create();

        $firstMessage = $conversation->messages()->oldestWithId()->first();
        $secondMessage = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $firstMessage->getSenderParticipation();
        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $sender = $senderParticipation->user;
        $recepient = $recepientParticipation->user;

        $secondMessage->user_deletetions()
            ->firstOrCreate(
                ['user_id' => $recepient->id],
                ['deleted_at' => now()]
            );

        $this->assertTrue($secondMessage->is($senderParticipation->getLatestMessage()));
        $this->assertTrue($firstMessage->is($recepientParticipation->getLatestMessage()));
    }

    public function testIfLatestNotViewedMessageDeletedForSender()
    {
        $conversation = Conversation::factory()->with_two_not_viewed_message()->create();

        $firstMessage = $conversation->messages()->oldestWithId()->first();
        $secondMessage = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $firstMessage->getSenderParticipation();
        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $sender = $senderParticipation->user;
        $recepient = $recepientParticipation->user;

        $secondMessage->delete();

        $this->assertTrue($firstMessage->is($senderParticipation->getLatestMessage()));
        $this->assertTrue($firstMessage->is($recepientParticipation->getLatestMessage()));
    }

    public function testIfLatestNotViewedMessageDeletedForRecepient()
    {
        $conversation = Conversation::factory()->with_two_not_viewed_message()->create();

        $firstMessage = $conversation->messages()->oldestWithId()->first();
        $secondMessage = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $firstMessage->getSenderParticipation();
        $recepientParticipation = $firstMessage->getFirstRecepientParticipation();

        $sender = $senderParticipation->user;
        $recepient = $recepientParticipation->user;

        $secondMessage->delete();

        $this->assertTrue($firstMessage->is($senderParticipation->getLatestMessage()));
        $this->assertTrue($firstMessage->is($recepientParticipation->getLatestMessage()));
    }
}
