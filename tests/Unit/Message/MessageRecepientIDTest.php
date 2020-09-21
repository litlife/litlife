<?php

namespace Tests\Unit\Message;

use App\Message;
use PHPUnit\Framework\TestCase;

class MessageRecepientIDTest extends TestCase
{
	public function test()
	{
		$message = new Message();
		$message->recepient_id = 100;

		$this->assertEquals(100, $message->recepient_id);
	}
}
