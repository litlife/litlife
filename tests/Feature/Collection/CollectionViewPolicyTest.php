<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\CollectionUser;
use App\Enums\StatusEnum;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class CollectionViewPolicyTest extends TestCase
{
    public function testViewPolicy()
    {
        $collection = Collection::factory()->private()->create()
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

        $this->assertTrue($creator->can('view', $collection));
        $this->assertFalse($friend->can('view', $collection));
        $this->assertFalse($subscriber->can('view', $collection));
        $this->assertFalse($nobody_user->can('view', $collection));

        $collection->status = StatusEnum::Private;
        $collection->save();
        $collection->refresh();

        $this->assertEquals(StatusEnum::Private, $collection->status);

        $collection->status = StatusEnum::Accepted;
        $collection->save();
        $collection->refresh();

        $this->assertTrue($creator->can('view', $collection));
        $this->assertTrue($friend->can('view', $collection));
        $this->assertTrue($subscriber->can('view', $collection));
        $this->assertTrue($nobody_user->can('view', $collection));
    }

    public function testCantViewIfNotCollectionUser()
    {
        $collection = Collection::factory()->private()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('view', $collection));
    }

    public function testCanViewIfCollectionUser()
    {
        $collection = Collection::factory()->private()->create();

        $user = User::factory()->create();

        $collectionUser = CollectionUser::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id
        ]);

        $user->refresh();

        $this->assertTrue($user->can('view', $collection));
    }
}
