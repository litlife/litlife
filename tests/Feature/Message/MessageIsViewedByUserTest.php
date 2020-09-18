<?php

namespace Tests\Feature\Message;

use App\Conversation;
use Tests\TestCase;

class MessageIsViewedByUserTest extends TestCase
{
	public function testTrueForRecepient()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();
		$recepient = $message->getFirstRecepientParticipation()->user;

		$this->assertTrue($message->isViewedByUser($recepient));
	}

	public function testTrueForSender()
	{
		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();
		$sender = $message->create_user;

		$this->assertTrue($message->isViewedByUser($sender));
	}

	public function testFalseForRecepient()
	{
		$conversation = factory(Conversation::class)
			->states('with_not_viewed_message')
			->create();

		$message = $conversation->messages()->first();
		$recepient = $message->getFirstRecepientParticipation()->user;

		$this->assertFalse($message->isViewedByUser($recepient));
	}

	public function testTrueForSenderIfCreator()
	{
		$conversation = factory(Conversation::class)
			->states('with_not_viewed_message')
			->create();

		$message = $conversation->messages()->first();
		$sender = $message->create_user;

		$this->assertTrue($message->isViewedByUser($sender));
	}
}
