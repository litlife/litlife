<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumEditPolicyTest extends TestCase
{
    public function testCantEditForumIfHasPermissions()
    {
        $admin = User::factory()->create();
        $admin->group->forum_edit_forum = false;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertFalse($admin->can('update', $forum));
    }

    public function testCanEditForumIfHasPermissions()
    {
        $admin = User::factory()->create();
        $admin->group->forum_edit_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertTrue($admin->can('update', $forum));
    }

}
