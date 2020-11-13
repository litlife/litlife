<?php

namespace Tests\Feature\Comment\Publish;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentPublishPolicyTest extends TestCase
{
    public function testCanPublishPrivate()
    {
        $comment = Comment::factory()->private()->create();

        $user = $comment->create_user;

        $this->assertTrue($user->can('publish', $comment));
    }

    public function testCantPublishNotPrivate()
    {
        $comment = Comment::factory()->accepted()->create();

        $user = $comment->create_user;

        $this->assertFalse($user->can('publish', $comment));
    }

    public function testCantPublishByOtherUser()
    {
        $comment = Comment::factory()->private()->create();

        $user = User::factory()->admin()->create();

        $this->assertFalse($user->can('publish', $comment));
    }

    public function testCantPublishTrashed()
    {
        $comment = Comment::factory()->private()->create();

        $user = $comment->create_user;

        $comment->delete();

        $this->assertFalse($user->can('publish', $comment));
    }
}
