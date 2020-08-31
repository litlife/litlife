<?php

namespace Tests\Feature\Comment;

use App\Book;
use App\User;
use Tests\TestCase;

class CommentCreateLeaveInPersonalAccess extends TestCase
{
	public function testStoreHttp()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				[
					'bb_text' => $text,
					'leave_for_personal_access' => '1'
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$comment = $book->comments()->first();

		$this->assertNotNull($comment);
		$this->assertTrue($comment->isPrivate());
	}
}
