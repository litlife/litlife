<?php

namespace Tests\Feature\Book\Sale;

use App\Author;
use App\User;
use Tests\TestCase;

class BookPurchaseTest extends TestCase
{
    public function testIsOk()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();

        $user = User::factory()->create();

        $this->assertTrue($user->can('buy', $book));

        $this->actingAs($user)
            ->get(route('books.purchase', $book))
            ->assertOk()
            ->assertViewIs('book.purchase')
            ->assertViewHas('book', $book);
    }
}
