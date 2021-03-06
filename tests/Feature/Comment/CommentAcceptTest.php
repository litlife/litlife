<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentAcceptTest extends TestCase
{
    public function testApprove()
    {
        $user = User::factory()->create();
        $user->group->check_post_comments = true;
        $user->push();

        foreach (Comment::sentOnReview()->get() as $comment) {
            $comment->forceDelete();
        }

        $this->assertEquals(0, Comment::getCachedOnModerationCount());

        $comment = Comment::factory()->create();
        $comment->statusSentForReview();
        $comment->save();

        Comment::flushCachedOnModerationCount();
        $this->assertEquals(1, Comment::getCachedOnModerationCount());

        $this->actingAs($user)
            ->get(route('comments.approve', compact('comment')))
            ->assertOk();

        $this->assertTrue($comment->fresh()->isAccepted());

        $this->assertEquals(0, Comment::getCachedOnModerationCount());
    }
}
