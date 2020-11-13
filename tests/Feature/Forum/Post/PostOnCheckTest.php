<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostOnCheckTest extends TestCase
{
    public function testOnCheck()
    {
        $user = User::factory()->create();
        $user->group->check_post_comments = true;
        $user->push();

        $post = Post::factory()->create();
        $post->statusSentForReview();
        $post->save();

        $this->actingAs($user)
            ->get(route('posts.on_check'))
            ->assertOk()
            ->assertSeeText($post->text);
    }
}
