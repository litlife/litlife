<?php

namespace Tests\Feature\Collection\User;

use App\Collection;
use App\CollectionUser;
use App\User;
use Tests\TestCase;

class CollectionUserEditPolicyTest extends TestCase
{
    public function testCollectionCreatorCanEditUser()
    {
        $collection = Collection::factory()->create();

        $this->assertTrue($collection->create_user->can('editUser', $collection));
    }

    public function testOtherUserCantEditUser()
    {
        $collection = Collection::factory()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('editUser', $collection));
    }

    public function testCollectionUserCanEditUserWithPermission()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertTrue($user->can('editUser', $collection));
    }

    public function testCollectionUserCantEditUserWithoutPermission()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => false]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertFalse($user->can('editUser', $collection));
    }

}
