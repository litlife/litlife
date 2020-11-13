<?php

namespace Tests\Feature\Collection\Book;

use App\Collection;
use App\CollectionUser;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class CollectionBookAttachPolicyTest extends TestCase
{
    public function testCollectionUserCanAddBooksWithPermission()
    {
        $collectionUser = CollectionUser::factory()->create(['can_add_books' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertTrue($user->can('addBook', $collection));
    }

    public function testCollectionUserCanAddBooksWithoutPermission()
    {
        $collectionUser = CollectionUser::factory()->collection_who_can_add_me()->create();

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertFalse($user->can('addBook', $collection));
    }

    public function testAddBookPolicy()
    {
        $collection = Collection::factory()->create(['who_can_add' => 'me'])
            ->fresh();

        $creator = $collection->create_user;

        $relation = UserRelation::factory()->create([
            'user_id' => $creator->id,
            'status' => UserRelationType::Friend
        ]);

        $friend = $relation->second_user;

        $relation = UserRelation::factory()->create([
            'user_id2' => $creator->id,
            'status' => UserRelationType::Subscriber
        ]);

        $subscriber = $relation->first_user;

        $nobody_user = User::factory()->create();

        $this->assertTrue($creator->can('addBook', $collection));
        //$this->assertFalse($friend->can('addBook', $collection));
        //$this->assertFalse($subscriber->can('addBook', $collection));
        $this->assertFalse($nobody_user->can('addBook', $collection));
        /*
                $collection->who_can_add = 'friends';
                $collection->save();
                $collection->refresh();

                $this->assertEquals(UserAccountPermissionValues::friends, $collection->who_can_add);

                $this->assertTrue($creator->can('addBook', $collection));
                $this->assertTrue($friend->can('addBook', $collection));
                $this->assertFalse($subscriber->can('addBook', $collection));
                $this->assertFalse($nobody_user->can('addBook', $collection));

                $collection->who_can_add = 'friends_and_subscribers';
                $collection->save();
                $collection->refresh();

                $this->assertTrue($creator->can('addBook', $collection));
                $this->assertTrue($friend->can('addBook', $collection));
                $this->assertTrue($subscriber->can('addBook', $collection));
                $this->assertFalse($nobody_user->can('addBook', $collection));
        */
        $collection->who_can_add = 'everyone';
        $collection->save();
        $collection->refresh();

        $this->assertTrue($creator->can('addBook', $collection));
        //$this->assertTrue($friend->can('addBook', $collection));
        //$this->assertTrue($subscriber->can('addBook', $collection));
        $this->assertTrue($nobody_user->can('addBook', $collection));
    }

    public function testAddBookPolicyManageCollections()
    {
        $collection = Collection::factory()->create(['who_can_add' => 'me'])
            ->fresh();

        $user = $collection->create_user;

        $this->assertTrue($user->can('addBook', $collection));

        $user->group->manage_collections = false;
        $user->push();
        $user->refresh();

        $this->assertFalse($user->can('addBook', $collection));
    }

}
