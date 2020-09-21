<?php

namespace Tests\Unit\Message;

use App\Message;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class MessageTrashedTest extends TestCase
{
	public function testFalseIfNull()
	{
		$message = new Message();
		$message->deleted_at = null;
		$message->message_deletions_deleted_at = null;

		$this->assertFalse($message->trashed());
	}

	public function testTrueIfMessageDeletedAtIsNotNull()
	{
		$message = new Message();
		$message->deleted_at = Carbon::now();

		$this->assertTrue($message->trashed());
	}

	public function testTrueIfMessageDeletionsDeletedAtIsNotNull()
	{
		$message = new Message();
		$message->message_deletions_deleted_at = Carbon::now();

		$this->assertTrue($message->trashed());
	}
}
