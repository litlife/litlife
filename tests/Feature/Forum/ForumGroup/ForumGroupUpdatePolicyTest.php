<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupUpdatePolicyTest extends TestCase
{
    public function tesCantIfUserHasPermission()
    {
        $admin = User::factory()->create();
        $admin->group->forum_group_handle = true;
        $admin->push();

        $forumGroup = ForumGroup::factory()->create();

        $this->assertTrue($admin->can('update', $forumGroup));
    }

    public function testCantIfUserDoesntHavePermission()
    {
        $user = User::factory()->create();
        $user->group->forum_group_handle = false;
        $user->push();

        $forumGroup = ForumGroup::factory()->create();

        $this->assertFalse($user->can('update', $forumGroup));
    }

}
