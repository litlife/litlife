<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupChangeOrderTest extends TestCase
{
    public function testIfUserHasPermission()
    {
        $admin = User::factory()->create();
        $admin->group->forum_group_handle = true;
        $admin->push();

        $this->assertTrue($admin->can('change_order', ForumGroup::class));
    }

    public function testIfUserDoesntHavePermission()
    {
        $user = User::factory()->create();
        $user->group->forum_group_handle = false;
        $user->push();

        $this->assertFalse($user->can('change_order', ForumGroup::class));
    }

}
