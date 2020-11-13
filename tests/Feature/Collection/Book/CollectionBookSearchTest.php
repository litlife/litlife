<?php

namespace Tests\Feature\Collection\Book;

use App\Book;
use App\CollectedBook;
use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionBookSearchTest extends TestCase
{
    public function testSearchById()
    {
        $collection = Collection::factory()->accepted()->create();

        $book = Book::factory()->create();

        $user = $collection->create_user;

        $this->actingAs($user)
            ->ajax()
            ->get(route('collections.books.list', ['collection' => $collection, 'query' => $book->id]))
            ->assertOk()
            ->assertViewIs('collection.book.list')
            ->assertSeeText($book->title);
    }

    public function testSearchByIsbn()
    {
        $user = User::factory()->admin()->create();

        $title = uniqid();
        $isbn = rand(100, 999).'-'.rand(1, 9).'-'.rand(100, 999).'-'.rand(10000, 99999).'-'.rand(1, 9);

        $collected = CollectedBook::factory()->create();

        $book = $collected->book;
        $book->title = $title;
        $book->pi_isbn = $isbn;
        $book->save();

        $collection = Collection::factory()->create();

        $this->actingAs($user)
            ->ajax()
            ->get(route('collections.books.list', ['collection' => $collection, 'query' => $isbn]))
            ->assertOk()
            ->assertSeeText($book->title)
            ->assertDontSeeText(__('In collection'));
    }

    public function testSearchByIsbnSeeBookInCollection()
    {
        $user = User::factory()->admin()->create();

        $title = uniqid();
        $isbn = rand(100, 999).'-'.rand(1, 9).'-'.rand(100, 999).'-'.rand(10000, 99999).'-'.rand(1, 9);

        $collected = CollectedBook::factory()->create();

        $collection = $collected->collection;
        $book = $collected->book;
        $book->title = $title;
        $book->pi_isbn = $isbn;
        $book->save();

        $this->actingAs($user)
            ->ajax()
            ->get(route('collections.books.list', ['collection' => $collection, 'query' => $isbn]))
            ->assertOk()
            ->assertSeeText($book->title)
            ->assertSeeText(__('In collection'));
    }
}
