<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class GreetingTest extends TestCase
{


	public function testEvening()
	{
		$user = factory(User::class)
			->create()
			->fresh();

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
		$user = factory(User::class)
			->create()
			->fresh();

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
		$user = factory(User::class)
			->create()
			->fresh();

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
		$user = factory(User::class)
			->create()
			->fresh();

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
