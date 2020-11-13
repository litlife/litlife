<?php

namespace Tests\Feature\Collection\Book;

use App\CollectionUser;
use Tests\TestCase;

class CollectionEditBookDescriptionTest extends TestCase
{
    public function testCollectionUserCanEditBookDescriptionWithPermission()
    {
        $collectionUser = CollectionUser::factory()->create(['can_edit_books_description' => true]);

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertTrue($user->can('editBookDescription', $collection));
    }

    public function testCollectionUserCanEditBookDescriptionWithoutPermission()
    {
        $collectionUser = CollectionUser::factory()->collection_who_can_add_me()->create();

        $user = $collectionUser->user;
        $collection = $collectionUser->collection;

        $this->assertFalse($user->can('editBookDescription', $collection));
    }
}
