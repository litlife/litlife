<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookFileEditPolicyTest extends TestCase
{
    public function testCanIfFilePrivate()
    {
        $book = Book::factory()->with_create_user()->create();

        $user = $book->create_user;

        $file = BookFile::factory()->odt()->private()->create(['create_user_id' => $user->id]);

        $user->group->add_book = true;
        $user->push();

        $this->assertTrue($user->can('update', $file));
    }

    public function testCanIfOnReview()
    {
        $book = Book::factory()->with_create_user()->create();

        $user = $book->create_user;

        $file = BookFile::factory()->odt()->sent_for_review()->create(['create_user_id' => $user->id]);

        $user->group->add_book = true;
        $user->push();

        $this->assertTrue($user->can('update', $file));
    }
}
