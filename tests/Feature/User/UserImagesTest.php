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
		$user = factory(User::class)
			->create();

		$user2 = factory(User::class)
			->create();

		$this->assertFalse($user2->can('view_images', $user));
		$this->assertTrue($user->can('view_images', $user));
	}
}
