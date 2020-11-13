<?php

namespace Tests\Feature\Book\Collection;

use App\Book;
use App\User;
use Tests\TestCase;

class BookCollectionAddToCollectionPolicyTest extends TestCase
{
    public function testCanIfBookAccepted()
    {
        $book = Book::factory()->accepted()->create();

        $user = User::factory()->create();

        $this->assertTrue($user->can('addToCollection', $book));
    }

    public function testCantIfBookPrivate()
    {
        $book = Book::factory()->private()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('addToCollection', $book));
    }
}
