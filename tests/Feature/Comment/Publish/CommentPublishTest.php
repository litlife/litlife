<?php

namespace Tests\Feature\Comment\Publish;

use App\Comment;
use Tests\TestCase;

class CommentPublishTest extends TestCase
{
    public function testPublishPrivate()
    {
        $comment = Comment::factory()->book()->private()->create();

        $this->assertTrue($comment->isPrivate());

        $comment->commentable->refreshCommentCount();
        $comment->commentable->save();

        $this->assertEquals(0, $comment->commentable->comment_count);

        $user = $comment->create_user;

        $this->actingAs($user)
            ->get(route('comments.publish', $comment))
            ->assertOk();

        $comment->refresh();

        $this->assertTrue($comment->isAccepted());

        $this->assertEquals(1, $comment->commentable->comment_count);
    }
}
