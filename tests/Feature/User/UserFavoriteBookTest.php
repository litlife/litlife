<?php

namespace Tests\Feature\User;

use App\Book;
use App\BookReadRememberPage;
use App\User;
use Tests\TestCase;

class UserFavoriteBookTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testToggle()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('books.favorites.toggle', $book))
            ->assertOk()
            ->assertJsonFragment(['result' => 'attached']);

        $user->refresh();
        $book->refresh();

        $this->assertEquals(1, $book->usersAddedToFavorites()->count());
        $this->assertEquals(1, $user->books()->count());
        $this->assertEquals(1, $book->added_to_favorites_count);
        $this->assertEquals(1, $user->user_lib_book_count);

        $this->actingAs($user)
            ->get(route('books.favorites.toggle', $book))
            ->assertOk()
            ->assertJsonFragment(['result' => 'detached']);

        $user->refresh();
        $book->refresh();

        $this->assertEquals(0, $book->usersAddedToFavorites()->count());
        $this->assertEquals(0, $user->books()->count());
        $this->assertEquals(0, $book->added_to_favorites_count);
        $this->assertEquals(0, $user->user_lib_book_count);
    }

    public function testFavoriteBooksUpdatesOnToggle()
    {
        $characters_count = rand(1000, 2000);

        $remembered_page = BookReadRememberPage::factory()->create(['characters_count' => $characters_count]);

        $user = $remembered_page->user;
        $book = $remembered_page->book;
        $book->characters_count = $characters_count;
        $book->save();
        $book->refresh();

        $this->assertEquals($book->characters_count, $remembered_page->characters_count);

        $this->actingAs($user)
            ->get(route('books.favorites.toggle', $book))
            ->assertOk()
            ->assertJsonFragment(['result' => 'attached']);

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());

        $this->actingAs($user)
            ->get(route('books.favorites.toggle', $book))
            ->assertOk()
            ->assertJsonFragment(['result' => 'detached']);

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());

        $book->characters_count = $remembered_page->characters_count + 1000;
        $book->save();

        $this->actingAs($user)
            ->get(route('books.favorites.toggle', $book))
            ->assertOk()
            ->assertJsonFragment(['result' => 'attached']);

        $this->assertEquals(1, $user->getFavoriteBooksWithUpdatesCount());

        $this->actingAs($user)
            ->get(route('books.favorites.toggle', $book))
            ->assertOk()
            ->assertJsonFragment(['result' => 'detached']);

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());
    }
}
