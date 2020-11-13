<?php

namespace Tests\Feature\Collection\User;

use App\CollectionUser;
use Tests\TestCase;

class CollectionUserDeleteTest extends TestCase
{
    public function testDeleteHttp()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->actingAs($user)
            ->get(route('collections.users.delete', ['collection' => $collection, 'user' => $user]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('collections.users.index', $collection));

        $collectionUser->refresh();
        $collection->refresh();

        $this->assertSoftDeleted($collectionUser);

        $this->assertEquals(1, $collection->users_count);
    }

    public function testRestoreHttp()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $collectionUser->delete();

        $this->actingAs($collection->create_user)
            ->get(route('collections.users.delete', ['collection' => $collection, 'user' => $user]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('collections.users.index', $collection));

        $collectionUser->refresh();
        $collection->refresh();

        $this->assertFalse($collectionUser->trashed());

        $this->assertEquals(2, $collection->users_count);
    }
}
