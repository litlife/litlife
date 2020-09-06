<?php

namespace Tests\Feature\Book\Attachment;

use App\Book;
use App\User;
use Tests\TestCase;

class BookAttachmentsPolicyTest extends TestCase
{
	public function testCreateAttachmentPolicy()
	{
		$admin = factory(User::class)->create();

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->push();

		$this->assertFalse($admin->can('create_attachment', $book));

		$admin->group->edit_self_book = true;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$this->assertTrue($admin->can('create_attachment', $book));
	}

}
