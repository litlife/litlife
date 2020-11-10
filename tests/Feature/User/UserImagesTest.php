<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class UserImagesTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */

	public function testPermissions()
	{
		$user = User::factory()->create();

		$user2 = User::factory()->create();

		$this->assertFalse($user2->can('view_images', $user));
		$this->assertTrue($user->can('view_images', $user));
	}

	public function testRouteIsOk()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('users.images.index', ['user' => $user]))
			->assertOk();
	}
}
