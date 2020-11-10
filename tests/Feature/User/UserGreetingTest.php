<?php

namespace Tests\Feature\User;

use App\Http\Middleware\RemeberSessionGeoIpAndBrowser;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserGreetingTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub

		$this->withMiddleware(RemeberSessionGeoIpAndBrowser::class);
	}

	public function testEvening()
	{
		$user = User::factory()->create();

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			18, 23, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_evening', ['name' => $user->userName]));

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			22, 40, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_evening', ['name' => $user->userName]));
	}

	public function testNight()
	{
		$user = User::factory()->create();

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			23, 10, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_night', ['name' => $user->userName]));

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			0, 30, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_night', ['name' => $user->userName]));

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			3, 30, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_night', ['name' => $user->userName]));
	}

	public function testMorning()
	{
		$user = User::factory()->create();

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			6, 10, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_morning', ['name' => $user->userName]));

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			8, 59, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_morning', ['name' => $user->userName]));

	}

	public function testDay()
	{
		$user = User::factory()->create();

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			11, 10, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_day', ['name' => $user->userName]));

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			15, 59, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_day', ['name' => $user->userName]));

		Carbon::setTestNow(Carbon::create(2020, 5, 21,
			17, 50, 00, 'Europe/Moscow'));

		session(['show_greeting' => true]);

		$this->actingAs($user)
			->get(route('home'))
			->assertSeeText(__('greeting.good_day', ['name' => $user->userName]));
	}
}
