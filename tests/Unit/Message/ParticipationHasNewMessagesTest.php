<?php

namespace Tests\Unit\Message;

use App\Participation;
use PHPUnit\Framework\TestCase;

class ParticipationHasNewMessagesTest extends TestCase
{
	public function testTrue()
	{
		$participation = new Participation();
		$participation->new_messages_count = 1;

		$this->assertTrue($participation->hasNewMessages());
	}

	public function testFalse()
	{
		$participation = new Participation();
		$participation->new_messages_count = 0;

		$this->assertFalse($participation->hasNewMessages());
	}
}
