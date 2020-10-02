<?php

namespace Tests\Feature\User\Email;

use App\Like;
use App\Notifications\NewLikeNotification;
use App\User;
use Tests\TestCase;

class UserNotificationEmailTest extends TestCase
{
	public function testFound()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();
		$email->notice = true;
		$email->save();

		$like = factory(Like::class)
			->create();

		$notification = new NewLikeNotification($like);

		$this->assertEquals($email->email, $user->routeNotificationForMail($notification));
	}

	public function testNotFound()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();
		$email->notice = false;
		$email->save();

		$like = factory(Like::class)
			->create();

		$notification = new NewLikeNotification($like);

		$this->assertNull($user->routeNotificationForMail($notification));
	}
}
