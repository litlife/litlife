<?php

namespace Tests\Feature\Collection\Book;

use App\CollectedBook;
use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionBookSelectTest extends TestCase
{
    public function testBooksSelectHttp()
    {
        $user = User::factory()
            ->admin()
            ->create();

        $collection = Collection::factory()
            ->accepted()
            ->create(['who_can_add' => 'everyone']);

        $this->actingAs($user)
            ->get(route('collections.books.select', $collection))
            ->assertOk()
            ->assertViewIs('collection.book.attach');
    }

    public function testBooksSelectMaxCollectionNumber()
    {
        $user = User::factory()
            ->admin()
            ->create();

        $number = rand(10, 100);

        $collected = CollectedBook::factory()->create(['number' => $number]);

        $collection = $collected->collection;
        $collection->who_can_add = 'everyone';
        $collection->save();

        $this->actingAs($user)
            ->get(route('collections.books.select', ['collection' => $collection]))
            ->assertOk()
            ->assertViewHas('max', $number + 1);
    }

    public function testIsOkIfOpenCollectionWithNoAccess()
    {
        $collection = Collection::factory()
            ->private()
            ->create();

        $this->get(route('collections.books.select', $collection))
            ->assertStatus(401);
    }
}
