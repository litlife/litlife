<?php

namespace Tests\Feature\Book\Attachment;

use App\Book;
use App\User;
use Tests\TestCase;

class AttachmentCreatePolicyTest extends TestCase
{
    public function testCantIfNoPermission()
    {
        $admin = User::factory()->create();
        $admin->group->edit_self_book = false;
        $admin->group->edit_other_user_book = false;
        $admin->push();

        $book = Book::factory()->accepted()->create();

        $this->assertFalse($admin->can('create_attachment', $book));
    }

    public function testCanIfHasPermission()
    {
        $admin = User::factory()->create();
        $admin->group->edit_self_book = true;
        $admin->group->edit_other_user_book = true;
        $admin->push();

        $book = Book::factory()->accepted()->create();

        $this->assertTrue($admin->can('create_attachment', $book));
    }
}
