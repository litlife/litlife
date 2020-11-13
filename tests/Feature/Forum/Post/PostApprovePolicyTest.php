<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostApprovePolicyTest extends TestCase
{
    public function testCanIfHasPermission()
    {
        $post = Post::factory()->sent_for_review()->create();

        $user = User::factory()->create();
        $user->group->check_post_comments = true;
        $user->push();

        $this->assertTrue($user->can('approve', $post));
    }

    public function testCantIfDoesntHavePermission()
    {
        $post = Post::factory()->sent_for_review()->create();

        $user = User::factory()->create();
        $user->group->check_post_comments = false;
        $user->push();

        $this->assertFalse($user->can('approve', $post));
    }

    public function testCantIfPostNotOnReview()
    {
        $post = Post::factory()->create();

        $user = User::factory()->admin()->create();

        $this->assertFalse($user->can('approve', $post));
    }
}
