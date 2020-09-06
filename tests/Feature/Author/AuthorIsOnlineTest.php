<?php

namespace Tests\Feature\Author;

use App\Author;
use Carbon\Carbon;
use Tests\TestCase;

class AuthorIsOnlineTest extends TestCase
{
	public function testIsOnlineIfManagerAccepted()
	{
		config(['litlife.user_last_activity' => 5]);

		$author = factory(Author::class)
			->states('with_author_manager', 'accepted')
			->create()
			->fresh();

		$user = $author->managers->first()->user;

		Carbon::setTestNow(now()->addMinutes(5)->addMinute());

		$this->assertFalse($user->isOnline());
		$this->assertFalse($author->isOnline());

		$user->last_activity_at = now();
		$user->save();

		$this->assertTrue($user->isOnline());
		$this->assertTrue($author->isOnline());

		Carbon::setTestNow(now()->addMinutes(5)->addMinute());

		$this->assertFalse($user->isOnline());
		$this->assertFalse($author->isOnline());
	}

	public function testIsOnlineIfManagerOnReview()
	{
		config(['litlife.user_last_activity' => 5]);

		$author = factory(Author::class)
			->states('with_author_manager_on_review', 'accepted')
			->create()
			->fresh();

		$manager = $author->managers->first();

		$this->assertTrue($manager->isSentForReview());

		$user = $manager->user;

		Carbon::setTestNow(now()->addMinutes(5)->addMinute());

		$this->assertFalse($user->isOnline());
		$this->assertFalse($author->isOnline());

		$user->last_activity_at = now();
		$user->save();

		$this->assertTrue($user->isOnline());
		$this->assertFalse($author->isOnline());
	}
}
