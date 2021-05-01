<?php

namespace Tests\Feature\User\Delete;

use App\User;
use Tests\TestCase;

class UserRestorePolicyTest extends TestCase
{
    public function testCanIfHasPermission()
    {
        $admin = User::factory()->create();
        $admin->group->user_delete = true;
        $admin->push();

        $user = User::factory()->deleted()->create();

        $this->assertTrue($admin->can('restore', $user));
    }

    public function testCantIfDoesntHavePermission()
    {
        $admin = User::factory()->create();
        $admin->group->user_delete = false;
        $admin->push();

        $user = User::factory()->deleted()->create();

        $this->assertFalse($admin->can('restore', $user));
    }

    public function testCantIfUserDeleted()
    {
        $admin = User::factory()->create();
        $admin->group->user_delete = true;
        $admin->push();

        $user = User::factory()->create();

        $this->assertFalse($admin->can('restore', $user));
    }
}
