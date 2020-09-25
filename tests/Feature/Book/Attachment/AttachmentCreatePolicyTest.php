<?php

namespace Tests\Feature\Book\Attachment;

use App\Book;
use App\User;
use Tests\TestCase;

class AttachmentCreatePolicyTest extends TestCase
{
	public function testCantIfNoPermission()
	{
		$admin = factory(User::class)->create();
		$admin->group->edit_self_book = false;
		$admin->group->edit_other_user_book = false;
		$admin->push();

		$book = factory(Book::class)
			->states('accepted')
			->create();

		$this->assertFalse($admin->can('create_attachment', $book));
	}

	public function testCanIfHasPermission()
	{
		$admin = factory(User::class)->create();
		$admin->group->edit_self_book = true;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$book = factory(Book::class)->states('accepted')->create();

		$this->assertTrue($admin->can('create_attachment', $book));
	}
}
