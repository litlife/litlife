<?php

namespace Tests\Feature\Book\Parse;

use App\Book;
use App\User;
use Tests\TestCase;

class BookParseCancelPolicyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCancelParsePolicy()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();
        $user->group->retry_failed_book_parse = true;
        $user->push();

        $book->parse->wait();
        $book->push();

        $this->assertTrue($user->can('cancel_parse', $book));

        $book->parse->start();
        $book->push();

        $this->assertFalse($user->can('cancel_parse', $book));

        $book->parse->failed(['error']);
        $book->push();

        $this->assertTrue($user->can('cancel_parse', $book));

        $book->parse->success();
        $book->push();

        $this->assertFalse($user->can('cancel_parse', $book));
    }

    public function testCantCancelParseIfPrivateBook()
    {
        $book = Book::factory()->with_create_user()->private()->create();

        $book->parse->wait();
        $book->push();

        $this->assertTrue($book->parse->isWait());

        $user = $book->create_user;

        $this->assertFalse($user->can('cancel_parse', $book));
    }
}
