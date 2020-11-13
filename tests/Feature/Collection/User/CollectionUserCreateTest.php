<?php

namespace Tests\Feature\Collection\User;

use App\Collection;
use App\CollectionUser;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionUserCreateTest extends TestCase
{
    public function testCreateHttp()
    {
        $collection = Collection::factory()->create();

        $this->actingAs($collection->create_user)
            ->get(route('collections.users.create', $collection))
            ->assertOk();
    }

    public function testStoreHttp()
    {
        $collection = Collection::factory()->create();

        $user = User::factory()->create();

        $description = Str::random(8);

        $this->actingAs($collection->create_user)
            ->post(route('collections.users.store', $collection), [
                'user_id' => $user->id,
                'description' => $description,
                'can_edit' => true,
                'can_add_books' => true,
                'can_remove_books' => true,
                'can_edit_books_description' => true,
                'can_comment' => true,
                'can_user_manage' => true,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('collections.users.index', $collection))
            ->assertSessionHas(['success' => __('collection_user.user_successfully_added')]);

        $collectionUser = $collection->collectionUser()->first();

        $this->assertNotNull($collectionUser);

        $collection->refresh();

        $this->assertEquals($collection->id, $collectionUser->collection_id);
        $this->assertEquals($user->id, $collectionUser->user_id);
        $this->assertTrue($collectionUser->can_edit);
        $this->assertTrue($collectionUser->can_add_books);
        $this->assertTrue($collectionUser->can_remove_books);
        $this->assertTrue($collectionUser->can_edit_books_description);
        $this->assertTrue($collectionUser->can_comment);
        $this->assertTrue($collectionUser->can_user_manage);
        $this->assertEquals($collection->create_user->id, $collectionUser->create_user->id);
        $this->assertEquals(2, $collection->users_count);
        $this->assertEquals($description, $collectionUser->description);
    }


    public function testCantAddUserIfAlreadyAdded()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $post = [
            'can_user_manage' => false,
            'can_edit' => false,
            'can_add_books' => false,
            'can_remove_books' => false,
            'can_edit_books_description' => false,
            'can_comment' => false,
            'user_id' => $user->id
        ];

        $response = $this->actingAs($collection->create_user)
            ->post(route('collections.users.store', ['collection' => $collection->id]), $post)
            ->assertRedirect();
        var_dump(session('errors'));
        $response->assertSessionHasErrors(['user_id' => __('collection.the_user_has_already_been_added')]);

        $collection->refresh();

        $this->assertEquals(2, $collection->users_count);
    }

    public function testCantAddUserIfItsCollectionCreator()
    {
        $collection = Collection::factory()->create();

        $post = [
            'can_user_manage' => false,
            'can_edit' => false,
            'can_add_books' => false,
            'can_remove_books' => false,
            'can_edit_books_description' => false,
            'can_comment' => false,
            'user_id' => $collection->create_user->id
        ];

        $response = $this->actingAs($collection->create_user)
            ->post(route('collections.users.store', ['collection' => $collection->id]), $post)
            ->assertRedirect();
        var_dump(session('errors'));
        $response->assertSessionHasErrors(['user_id' => __('collection.the_user_has_already_been_added')]);

        $collection->refresh();

        $this->assertEquals(1, $collection->users_count);
    }

    public function testAutoRestoreUserHttp()
    {
        $collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $collectionUser->delete();
        $this->assertTrue($collectionUser->trashed());

        $post = [
            'can_user_manage' => false,
            'can_edit' => false,
            'can_add_books' => false,
            'can_remove_books' => false,
            'can_edit_books_description' => false,
            'can_comment' => false,
            'user_id' => $user->id
        ];

        $this->actingAs($collection->create_user)
            ->post(route('collections.users.store', ['collection' => $collection]), $post)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('collections.users.index', $collection));

        $collectionUser->refresh();
        $collection->refresh();

        $this->assertFalse($collectionUser->trashed());
        $this->assertEquals(2, $collection->users_count);
    }
}
