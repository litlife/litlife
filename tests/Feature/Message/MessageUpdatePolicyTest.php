<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class MessageUpdatePolicyTest extends TestCase
{
    public function testPolicy()
    {
        $sender = User::factory()->create();

        $recepient = User::factory()->create();

        $message = Message::factory()->create([
            'create_user_id' => $sender->id,
            'recepient_id' => $recepient->id
        ]);

        $this->assertTrue($sender->can('update', $message));
        $this->assertFalse($recepient->can('update', $message));

        $time = now()->addMinutes(config('litlife.time_that_can_edit_message'))->addMinute();
        Carbon::setTestNow($time);

        $this->assertFalse($sender->can('update', $message));
        $this->assertFalse($recepient->can('update', $message));
    }
}
