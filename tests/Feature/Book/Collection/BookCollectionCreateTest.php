<?php

namespace Tests\Feature\Book\Collection;

use App\Book;
use App\Collection;
use App\User;
use Tests\TestCase;

class BookCollectionCreateTest extends TestCase
{
    public function test()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('books.collections.create', ['book' => $book]))
            ->assertOk()
            ->assertViewHas('book', $book)
            ->assertViewHas('collection', null);
    }

    public function testHasOldCollectionId()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();

        $collection = Collection::factory()->create();

        $this->actingAs($user)
            ->withOldInput('collection_id', $collection->id)
            ->get(route('books.collections.create', ['book' => $book]), [
                'collection_id' => $collection->id
            ])
            ->assertOk()
            ->assertViewHas('book', $book)
            ->assertViewHas('collection', $collection);
    }
}
