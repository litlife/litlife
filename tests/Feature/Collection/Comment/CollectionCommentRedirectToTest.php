<?php

namespace Tests\Feature\Collection\Comment;

use App\Comment;
use Tests\TestCase;

class CollectionCommentRedirectToTest extends TestCase
{
	public function testRedirectToComment()
	{
		$comment = Comment::factory()->collection()->create();

		$this->assertTrue($comment->isCollectionType());

		$response = $this->get(route('comments.go', $comment))
			->assertRedirect(route('collections.comments', [
				'collection' => $comment->commentable,
				'page' => 1,
				'comment' => $comment,
				'#comment_' . $comment->id
			]));
	}
}
