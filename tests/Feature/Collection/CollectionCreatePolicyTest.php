<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionCreatePolicyTest extends TestCase
{
    public function testCreatePolicy()
    {
        $user = User::factory()->create();
        $user->group->manage_collections = true;
        $user->push();
        $user->refresh();

        $this->assertTrue($user->can('create', Collection::class));

        $user->group->manage_collections = false;
        $user->push();
        $user->refresh();

        $this->assertFalse($user->can('create', Collection::class));
    }
}
