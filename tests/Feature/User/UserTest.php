<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
	public function testIndexHttp()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('users'))
			->assertOk();
	}

	public function testFulltextSearch()
	{
		$author = User::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testUseShopPolicy()
	{
		$user = User::factory()->create();

		$this->assertTrue($user->group->shop_enable);

		$this->assertTrue($user->can('use_shop', User::class));

		$user->group->shop_enable = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('use_shop', User::class));
	}

	public function testRefreshCounters()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('users.refresh_counters', ['user' => $user]))
			->assertRedirect(route('profile', ['user' => $user]));
	}
}
