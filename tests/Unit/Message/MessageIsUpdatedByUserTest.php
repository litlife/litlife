<?php

namespace Tests\Unit\Message;

use App\Message;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class MessageIsUpdatedByUserTest extends TestCase
{
	public function testTrue()
	{
		$message = new Message();
		$message->user_updated_at = Carbon::now();

		$this->assertTrue($message->isUpdatedByUser());
	}

	public function testFalse()
	{
		$message = new Message();
		$message->user_updated_at = null;

		$this->assertFalse($message->isUpdatedByUser());
	}
}
