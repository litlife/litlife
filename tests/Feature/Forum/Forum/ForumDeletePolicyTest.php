<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumDeletePolicyTest extends TestCase
{
    public function testCanIfHasPermissions()
    {
        $admin = User::factory()->create();
        $admin->group->delete_forum_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertTrue($admin->can('delete', $forum));

        $forum->delete();

        $this->assertTrue($admin->can('restore', $forum));
    }

    public function testCantIfDoesntHavePermissions()
    {
        $admin = User::factory()->create();
        $admin->group->delete_forum_forum = false;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertFalse($admin->can('delete', $forum));

        $forum->delete();

        $this->assertFalse($admin->can('restore', $forum));
    }
}
