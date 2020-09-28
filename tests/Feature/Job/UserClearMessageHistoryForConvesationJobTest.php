<?php

namespace Tests\Feature\Job;

use App\Conversation;
use App\Jobs\User\UserClearMessageHistoryForConvesationJob;
use App\User;
use Tests\TestCase;

class UserClearMessageHistoryForConvesationJobTest extends TestCase
{
	public function testException()
	{
		$user = factory(User::class)
			->create();

		$conversation = factory(Conversation::class)
			->states('with_two_users')
			->create();

		$this->expectExceptionMessage('The user must participate in the conversation');

		UserClearMessageHistoryForConvesationJob::dispatch($user, $conversation);
	}

	public function testDelete()
	{
		$user = factory(User::class)
			->create();

		$conversation = factory(Conversation::class)
			->states('with_viewed_message')
			->create();

		$message = $conversation->messages()->first();

		$sender = $message->create_user;
		$recepient = $message->getFirstRecepientParticipation();

		UserClearMessageHistoryForConvesationJob::dispatch($sender, $conversation);

		$message->refresh();

		$this->assertTrue($message->isDeletedForUser($sender));
		$this->assertFalse($message->isDeletedForUser($recepient));
	}
}
