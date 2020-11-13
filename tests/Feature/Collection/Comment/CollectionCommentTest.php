<?php

namespace Tests\Feature\Collection\Comment;

use App\Comment;
use Tests\TestCase;

class CollectionCommentTest extends TestCase
{
    public function testCollectionComment()
    {
        $comment = Comment::factory()->collection()->create();

        $this->assertEquals(18, $comment->commentable_type);
        $this->assertEquals('Collection', $comment->getCommentableModelName());
        $this->assertTrue($comment->isCollectionType());
    }
}
