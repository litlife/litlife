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
        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

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
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('ban', $admin));
    }

    public function testCantBanOtherAdmin()
    {
        $admin = User::factory()->admin()->create();

        $admin2 = User::factory()->admin()->create();

        $this->assertFalse($admin->can('ban', $admin2));
    }

    public function testCantBanIfPermissionEnable()
    {
        $admin = User::factory()->create();

        $user = User::factory()->create();

        $this->assertFalse($admin->can('ban', $user));

        $admin->group->change_users_group = true;
        $admin->push();

        $this->assertTrue($admin->can('ban', $user));
    }

    public function testCantBanIfUserAlreadyBanned()
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

        $this->assertTrue($admin->can('ban', $user));

        $group = UserGroup::where('key', UserGroupEnum::Banned)->firstOrFail();
        $user->groups()->sync([$group->id]);
        $user->save();
        $user->refresh();

        $this->assertFalse($admin->can('ban', $user));
    }
}