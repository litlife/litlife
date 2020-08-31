<?php

namespace Tests\Feature\User;

use App\Enums\UserGroupEnum;
use App\User;
use App\UserGroup;
use Tests\TestCase;

class UserBanTest extends TestCase
{
	public function testBan()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($admin)
			->get(route('users.ban', $user))
			->assertRedirect(route('profile', $user))
			->assertSessionHas(['success' => __('user.user_is_banned')]);

		$user->refresh();

		$this->assertEquals(1, $user->groups()->count());

		$group = $user->groups()->first();

		$this->assertEquals(UserGroupEnum::Banned, $group->key);
	}

	public function testCantBanSelf()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($admin->can('ban', $admin));
	}

	public function testCantBanOtherAdmin()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$admin2 = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($admin->can('ban', $admin2));
	}

	public function testCantBanIfPermissionEnable()
	{
		$admin = factory(User::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($admin->can('ban', $user));

		$admin->group->change_users_group = true;
		$admin->push();

		$this->assertTrue($admin->can('ban', $user));
	}

	public function testCantBanIfUserAlreadyBanned()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$user = factory(User::class)->create();

		$this->assertTrue($admin->can('ban', $user));

		$group = UserGroup::where('key', UserGroupEnum::Banned)->firstOrFail();
		$user->groups()->sync([$group->id]);
		$user->save();
		$user->refresh();

		$this->assertFalse($admin->can('ban', $user));
	}
}