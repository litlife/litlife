<?php

namespace Tests\Feature\Message;

use App\Conversation;
use Tests\TestCase;

class MessageIsViewedByUserTest extends TestCase
{
	public function testTrueForRecepient()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();
		$recepient = $message->getFirstRecepientParticipation()->user;

		$this->assertTrue($message->isViewedByUser($recepient));
	}

	public function testTrueForSender()
	{
		$conversation = Conversation::factory()->with_viewed_message()->create();

		$message = $conversation->messages()->first();
		$sender = $message->create_user;

		$this->assertTrue($message->isViewedByUser($sender));
	}

	public function testFalseForRecepient()
	{
		$conversation = Conversation::factory()->with_not_viewed_message()->create();

		$message = $conversation->messages()->first();
		$recepient = $message->getFirstRecepientParticipation()->user;

		$this->assertFalse($message->isViewedByUser($recepient));
	}

	public function testTrueForSenderIfCreator()
	{
		$conversation = Conversation::factory()->with_not_viewed_message()->create();

		$message = $conversation->messages()->first();
		$sender = $message->create_user;

		$this->assertTrue($message->isViewedByUser($sender));
	}
}
