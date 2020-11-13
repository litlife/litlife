<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookComplainTest extends TestCase
{
    public function testCantComplainForPrivateBook()
    {
        $user = User::factory()->create();
        $user->group->complain = true;
        $user->push();
        $user->refresh();

        $book = Book::factory()->private()->create();

        $this->assertFalse($user->can('complain', $book));
    }

    public function testCantComplainForAcceptedBook()
    {
        $user = User::factory()->create();
        $user->group->complain = true;
        $user->push();
        $user->refresh();

        $book = Book::factory()->accepted()->create();

        $this->assertTrue($user->can('complain', $book));
    }
}
