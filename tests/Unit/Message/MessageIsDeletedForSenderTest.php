<?php

namespace Tests\Unit\Message;

use App\Message;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class MessageIsDeletedForSenderTest extends TestCase
{
    public function testTrue()
    {
        $message = new Message();
        $message->deleted_at_for_created_user = Carbon::now();

        $this->assertTrue($message->isDeletedForSender());
    }

    public function testFalse()
    {
        $message = new Message();
        $message->deleted_at_for_created_user = null;

        $this->assertFalse($message->isDeletedForSender());
    }
}
