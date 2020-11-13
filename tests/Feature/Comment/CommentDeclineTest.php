<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentDeclineTest extends TestCase
{
    public function testDecline()
    {
        $user = User::factory()->create();
        $user->group->delete_my_comment = true;
        $user->group->delete_other_user_comment = true;
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
            ->delete(route('comments.destroy', ['comment' => $comment]))
            ->assertOk();

        $this->assertTrue($comment->fresh()->trashed());

        $this->assertEquals(0, Comment::getCachedOnModerationCount());
    }
}
