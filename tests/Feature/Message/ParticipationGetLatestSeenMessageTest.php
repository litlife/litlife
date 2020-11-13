<?php

namespace Tests\Feature\Message;

use App\Conversation;
use Tests\TestCase;

class ParticipationGetLatestSeenMessageTest extends TestCase
{
    public function testIfMessageViewed()
    {
        $conversation = Conversation::factory()->with_two_viewed_message()->create();

        $message = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $message->getSenderParticipation();
        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertTrue($message->is($senderParticipation->latest_seen_message));
        $this->assertTrue($message->is($recepientParticipation->latest_seen_message));
    }

    public function testIfMessageNotViewed()
    {
        $conversation = Conversation::factory()->with_two_not_viewed_message()->create();

        $message = $conversation->messages()->latestWithId()->first();

        $senderParticipation = $message->getSenderParticipation();
        $recepientParticipation = $message->getFirstRecepientParticipation();

        $this->assertTrue($message->is($senderParticipation->latest_seen_message));
        $this->assertNull($recepientParticipation->latest_seen_message);
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

        $this->assertTrue($secondMessage->is($senderParticipation->latest_seen_message));
        $this->assertTrue($secondMessage->is($recepientParticipation->latest_seen_message));
    }
}
