<?php

namespace Tests\Feature\Book\Sale;

use App\Author;
use Tests\TestCase;

class BookSellPolicyTest extends TestCase
{
    public function testSellPolicy()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->is_si = true;
        $book->is_lp = false;
        $book->push();

        $this->assertTrue($user->can('sell', $book));

        $author = Author::factory()->with_author_manager()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->is_si = true;
        $book->is_lp = false;
        $book->push();

        $this->assertFalse($user->can('sell', $book));
    }

    public function testAuthorCantSellIfNotCreatorOfTheBookPolicy()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();

        $this->assertFalse($user->can('sell', $book));
    }

    public function testAuthorCanSellIfUserCreatorOfTheBookPolicy()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->is_si = true;
        $book->is_lp = false;
        $book->push();

        $this->assertTrue($user->can('sell', $book));
    }

    public function testAuthorCantSellIfBookDeletedPolicy()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();
        $book->delete();
        $book->refresh();

        $this->assertFalse($user->can('sell', $book));
    }

    public function testCanSellPublishedBook()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->is_si = false;
        $book->is_lp = false;
        $book->push();

        $this->assertTrue($user->can('sell', $book));
    }

    public function testCantSellLpBook()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->is_si = false;
        $book->is_lp = true;
        $book->push();

        $this->assertFalse($user->can('sell', $book));
    }
}
