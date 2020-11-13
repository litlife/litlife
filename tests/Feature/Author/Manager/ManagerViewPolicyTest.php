<?php

namespace Tests\Feature\Author\Manager;

use App\Manager;
use App\User;
use Tests\TestCase;

class ManagerViewPolicyTest extends TestCase
{
    public function testCanIfUserManager()
    {
        $manager = Manager::factory()->create();

        $user = $manager->user;
        $author = $manager->manageable;

        $this->assertTrue($user->can('view', $manager));
    }

    public function testCantIfOtherUser()
    {
        $manager = Manager::factory()->create();

        $author = $manager->manageable;

        $user = User::factory()->create();
        $user->group->moderator_add_remove = false;
        $user->push();

        $this->assertFalse($user->can('view', $manager));
    }

    public function testCanIfAdmin()
    {
        $manager = Manager::factory()->create();

        $author = $manager->manageable;

        $user = User::factory()->admin()->create();

        $this->assertTrue($user->can('view', $manager));
    }
}
