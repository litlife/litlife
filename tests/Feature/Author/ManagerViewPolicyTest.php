<?php

namespace Tests\Feature\Author;

use App\Manager;
use App\User;
use Tests\TestCase;

class ManagerViewPolicyTest extends TestCase
{
	public function testCanIfUserManager()
	{
		$manager = factory(Manager::class)
			->create();

		$user = $manager->user;
		$author = $manager->manageable;

		$this->assertTrue($user->can('view', $manager));
	}

	public function testCantIfOtherUser()
	{
		$manager = factory(Manager::class)
			->create();

		$author = $manager->manageable;

		$user = factory(User::class)->create();
		$user->group->moderator_add_remove = false;
		$user->push();

		$this->assertFalse($user->can('view', $manager));
	}

	public function testCanIfAdmin()
	{
		$manager = factory(Manager::class)
			->create();

		$author = $manager->manageable;

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($user->can('view', $manager));
	}
}
