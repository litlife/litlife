<?php

namespace Tests\Feature\Collection\User;

use App\Collection;
use App\CollectionUser;
use App\User;
use Tests\TestCase;

class CollectionUserDeletePolicyTest extends TestCase
{
    public function testCollectionCreatorCanDeleteUser()
    {
        $collection = Collection::factory()->create();

        $this->assertTrue($collection->create_user->can('deleteUser', $collection));
    }

    public function testOtherUserCantDeleteUser()
    {
        $collection = Collection::factory()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('deleteUser', $collection));
    }

    public function testCollectionUserCanDeleteUserWithPermission()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertTrue($user->can('deleteUser', $collection));
    }

    public function testCollectionUserCantDeleteUserWithoutPermission()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => false]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertFalse($user->can('deleteUser', $collection));
    }
}
