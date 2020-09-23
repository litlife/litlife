<?php

namespace Tests\Feature\Collection\Comment;

use App\Comment;
use Tests\TestCase;

class CollectionCommentTest extends TestCase
{
	public function testCollectionComment()
	{
		$comment = factory(Comment::class)
			->states('collection')
			->create(['commentable_type' => 18]);

		$this->assertEquals(18, $comment->commentable_type);
		$this->assertEquals('Collection', $comment->getCommentableModelName());
		$this->assertTrue($comment->isCollectionType());
	}
}
