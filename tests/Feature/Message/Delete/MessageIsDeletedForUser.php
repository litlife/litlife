<?php

namespace Tests\Feature\Message\Delete;

use App\Message;
use App\MessageDelete;
use App\User;
use Tests\TestCase;

class MessageIsDeletedForUser extends TestCase
{
    public function testTrueIfFoundInUserDeletitions()
    {
        $recepient = User::factory()->create();

        $message = Message::factory()->create(['recepient_id' => $recepient->id]);

        $deletion = MessageDelete::factory()->create([
            'user_id' => $recepient->id,
            'message_id' => $message->id,
        ]);

        $this->assertTrue($message->isDeletedForUser($recepient));
    }

    public function testFalseIfFoundInUserDeletitions()
    {
        $recepient = User::factory()->create();

        $message = Message::factory()->create(['recepient_id' => $recepient->id]);

        $deletion = MessageDelete::factory()->create([
            'user_id' => $recepient->id,
            'message_id' => $message->id,
        ]);

        $user = User::factory()->create();

        $this->assertFalse($message->isDeletedForUser($user));
    }
}
