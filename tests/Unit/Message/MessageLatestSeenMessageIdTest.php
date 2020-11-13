<?php

namespace Tests\Unit\Message;

use App\Message;
use PHPUnit\Framework\TestCase;

class MessageLatestSeenMessageIdTest extends TestCase
{
    public function testDefault()
    {
        $message = new Message();

        $this->assertEquals(null, $message->latest_seen_message_id);
    }

    public function testZeroToNull()
    {
        $message = new Message();
        $message->latest_seen_message_id = 0;

        $this->assertEquals(null, $message->latest_seen_message_id);
    }

    public function testInteger()
    {
        $message = new Message();
        $message->latest_seen_message_id = 123;

        $this->assertEquals(123, $message->latest_seen_message_id);
    }
}
