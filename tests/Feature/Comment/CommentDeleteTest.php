<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentDeleteTest extends TestCase
{
    public function testIsOkIfBookSoftDeleted()
    {
        $user = User::factory()->admin()->create();

        $comment = Comment::factory()->create();

        $book = $comment->commentable;
        $book->delete();

        $this->actingAs($user)
            ->delete(route('comments.destroy', $comment))
            ->assertOk();

        $comment->refresh();

        $this->assertSoftDeleted($comment);
    }

    public function testIsOkIfBookForceDeleted()
    {
        $user = User::factory()->admin()->create();

        $comment = Comment::factory()->create();

        $book = $comment->commentable;
        $book->forceDelete();

        $this->actingAs($user)
            ->delete(route('comments.destroy', $comment))
            ->assertOk();

        $comment->refresh();

        $this->assertSoftDeleted($comment);
    }

    public function testIfCreatorDeleted()
    {
        $comment = Comment::factory()->book()->create();

        $comment->create_user->delete();
        $comment->refresh();
        $comment->delete();

        $this->assertTrue($comment->trashed());

        $comment = Comment::factory()->book()->create();

        $comment->create_user->forceDelete();
        $comment->refresh();
        $comment->delete();

        $this->assertTrue($comment->trashed());
    }
}
