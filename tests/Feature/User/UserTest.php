<?php

namespace Tests\Feature\User;

use App\Book;
use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
	public function testIndexHttp()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users'))
			->assertOk();
	}

	public function testFulltextSearch()
	{
		$author = User::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testSetCookiePassAgeRestriction()
	{
		$age = 12;

		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create(['age' => 18]);

		$this->actingAs($user)
			->get(route('user_pass_age_restriction', ['age' => $age]))
			->assertOk()
			->assertJson(['pass_age' => $age])
			->assertCookie('pass_age', $age)
			->assertCookieNotExpired('pass_age');
	}

	public function testUseShopPolicy()
	{
		$user = factory(User::class)
			->create();

		$this->assertTrue($user->group->shop_enable);

		$this->assertTrue($user->can('use_shop', User::class));

		$user->group->shop_enable = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('use_shop', User::class));
	}

	public function testRefreshCounters()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.refresh_counters', ['user' => $user]))
			->assertRedirect(route('profile', ['user' => $user]));
	}
}
