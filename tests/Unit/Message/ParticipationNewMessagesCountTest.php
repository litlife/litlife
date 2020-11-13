<?php

namespace Tests\Unit\Message;

use App\Participation;
use PHPUnit\Framework\TestCase;

class ParticipationNewMessagesCountTest extends TestCase
{
    public function testDefault()
    {
        $participation = new Participation();

        $this->assertEquals(0, $participation->new_messages_count);
    }

    public function testInteger()
    {
        $participation = new Participation();
        $participation->new_messages_count = 123.55;

        $this->assertEquals(123, $participation->new_messages_count);
    }

    public function testNegativeValue()
    {
        $participation = new Participation();
        $participation->new_messages_count = -123;

        $this->assertEquals(0, $participation->new_messages_count);
    }
}
