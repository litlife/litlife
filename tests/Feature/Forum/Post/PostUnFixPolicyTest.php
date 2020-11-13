<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostUnFixPolicyTest extends TestCase
{
    public function testCanIfHasPermission()
    {
        $user = User::factory()->create();
        $user->group->forum_post_manage = true;
        $user->push();

        $post = Post::factory()->fixed()->create();

        $this->assertTrue($user->can('unfix', $post));
    }

    public function testCantIfDoesntHavePermission()
    {
        $user = User::factory()->create();
        $user->group->forum_post_manage = false;
        $user->push();

        $post = Post::factory()->fixed()->create();

        $this->assertFalse($user->can('unfix', $post));
    }

    public function testCantIfUnFixed()
    {
        $user = User::factory()->create();
        $user->group->forum_post_manage = true;
        $user->push();

        $post = Post::factory()->create();

        $this->assertFalse($user->can('unfix', $post));
    }
}
