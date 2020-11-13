<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\BookKeyword;
use App\User;
use Tests\TestCase;

class BookKeywordDeletePolicyTest extends TestCase
{
    public function testCantIfNoPermission()
    {
        $user = User::factory()->create();

        $book_keyword = BookKeyword::factory()->create();

        $this->assertFalse($user->can('delete', $book_keyword));
    }

    public function testCanIfHasPermission()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_remove = true;
        $user->push();

        $book_keyword = BookKeyword::factory()->create();

        $this->assertTrue($user->can('delete', $book_keyword));
    }

    public function testCanIfBookPrivate()
    {
        $user = User::factory()->create();

        $book = Book::factory()
            ->private()
            ->create(['create_user_id' => $user->id]);

        $book_keyword = BookKeyword::factory()
            ->private()
            ->make(['create_user_id' => $user->id]);

        $book->book_keywords()->save($book_keyword);

        $this->assertTrue($user->can('delete', $book_keyword));
    }

    public function testCanDeleteIfOnReviewAndUserCreator()
    {
        $book_keyword = BookKeyword::factory()->sent_for_review()->create();

        $user = $book_keyword->create_user;

        $this->assertTrue($user->can('delete', $book_keyword));
    }

    public function testCantDeleteIfAcceptedAndUserCreator()
    {
        $book_keyword = BookKeyword::factory()->accepted()->create();

        $user = $book_keyword->create_user;

        $this->assertFalse($user->can('delete', $book_keyword));
    }
}
