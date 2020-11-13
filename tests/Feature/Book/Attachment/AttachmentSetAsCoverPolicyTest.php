<?php

namespace Tests\Feature\Book\Attachment;

use App\Book;
use App\User;
use Tests\TestCase;

class AttachmentSetAsCoverPolicyTest extends TestCase
{
    public function testCanIfBookPrivate()
    {
        $book = Book::factory()->private()->with_create_user()->with_attachment()->create();

        $attachment = $book->attachments()->first();

        $user = $book->create_user;

        $this->assertTrue($user->can('setAsCover', $attachment));
    }

    public function testCantIfAlreadyCover()
    {
        $book = Book::factory()->private()->with_create_user()->with_cover()->create();

        $attachment = $book->attachments()->first();

        $user = $book->create_user;

        $this->assertFalse($user->can('setAsCover', $attachment));
    }

    public function testCanIfHasPermission()
    {
        $book = Book::factory()->accepted()->with_create_user()->with_attachment()->create();

        $attachment = $book->attachments()->first();

        $user = User::factory()->create();
        $user->group->edit_other_user_book = true;
        $user->push();

        $this->assertTrue($user->can('setAsCover', $attachment));
    }

    public function testCantIfDoesntHavePermission()
    {
        $book = Book::factory()->accepted()->with_create_user()->with_attachment()->create();

        $attachment = $book->attachments()->first();

        $user = User::factory()->create();
        $user->group->edit_other_user_book = false;
        $user->push();

        $this->assertFalse($user->can('setAsCover', $attachment));
    }
}
