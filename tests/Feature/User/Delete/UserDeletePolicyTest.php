<?php

namespace Tests\Feature\User\Delete;

use App\User;
use Tests\TestCase;

class UserDeletePolicyTest extends TestCase
{
    public function testCanIfHasPermission()
    {
        $admin = User::factory()->create();
        $admin->group->user_delete = true;
        $admin->push();

        $user = User::factory()->create();

        $this->assertTrue($admin->can('delete', $user));
    }

    public function testCantIfDoesntHavePermission()
    {
        $admin = User::factory()->create();
        $admin->group->user_delete = false;
        $admin->push();

        $user = User::factory()->create();

        $this->assertFalse($admin->can('delete', $user));
    }

    public function testCantIfUserDeleted()
    {
        $admin = User::factory()->create();
        $admin->group->user_delete = true;
        $admin->push();

        $user = User::factory()->deleted()->create();

        $this->assertFalse($admin->can('delete', $user));
    }
}
