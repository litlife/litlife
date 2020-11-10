<?php

namespace Tests\Feature\User;

use App\Http\Middleware\RefreshUserLastActivity;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserOnlineTest extends TestCase
{
	public function testDripOnline()
	{
		$this->withMiddleware(RefreshUserLastActivity::class);

		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$now = now();

		$this->assertTrue($user->fresh()->isOnline());

		for ($a = 0; $a < 5; $a++) {
			$now = $now->addSeconds(config('genealabs-laravel-caffeine.drip-interval') / 1000)
				->addSecond();

			Carbon::setTestNow($now);

			$this->get(route('drip'))
				->assertStatus(204);

			$this->assertTrue($user->fresh()->isOnline());
		}

		$now = $now->addMinutes(config('litlife.user_last_activity'))
			->subSeconds(10);

		Carbon::setTestNow($now);

		$this->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$now = $now->addSeconds(config('genealabs-laravel-caffeine.drip-interval') / 1000);

		Carbon::setTestNow($now);

		$this->get(route('drip'))
			->assertStatus(204);

		$this->assertTrue($user->fresh()->isOnline());
	}

	public function testOfflineAfterActivityExpired()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$now = now();

		$this->assertTrue($user->fresh()->isOnline());

		$now = $now->addMinutes(config('litlife.user_last_activity'))
			->addSeconds(5);

		Carbon::setTestNow($now);

		$this->assertFalse($user->fresh()->isOnline());
	}
}
