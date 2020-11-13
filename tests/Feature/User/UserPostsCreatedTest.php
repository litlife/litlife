<?php

namespace Tests\Feature\User;

use App\Post;
use App\User;
use App\UsersAccessToForum;
use Tests\TestCase;

class UserPostsCreatedTest extends TestCase
{
    public function testViewPrivateMessageOnUserPostsList()
    {
        $post = Post::factory()->create();

        $forum = $post->forum;
        $forum->private = true;
        $forum->save();

        $usersAccessToForum = new UsersAccessToForum;
        $usersAccessToForum->user_id = $post->create_user_id;
        $forum->user_access()->save($usersAccessToForum);

        $response = $this->actingAs($post->create_user)
            ->get(route('users.posts', $post->create_user))
            ->assertSeeText($post->text);

        $other_user = User::factory()->create();

        $response = $this->actingAs($other_user)
            ->get(route('users.posts', $post->create_user))
            ->assertDontSeeText($post->text);

        $response = $this
            ->get(route('users.posts', $post->create_user))
            ->assertDontSeeText($post->text);
    }
}
