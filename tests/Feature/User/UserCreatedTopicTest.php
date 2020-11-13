<?php

namespace Tests\Feature\User;

use App\Post;
use App\Topic;
use App\User;
use App\UsersAccessToForum;
use Tests\TestCase;

class UserCreatedTopicTest extends TestCase
{
    public function testViewPrivateTopicOnUserTopicsList()
    {
        $post = Post::factory()->create();

        $forum = $post->forum;
        $forum->private = true;
        $forum->save();

        $topic = $post->topic;

        $usersAccessToForum = new UsersAccessToForum;
        $usersAccessToForum->user_id = $post->create_user_id;
        $forum->user_access()->save($usersAccessToForum);

        $response = $this->actingAs($post->create_user)
            ->get(route('users.topics', $topic->create_user))
            ->assertSeeText($topic->name);

        $other_user = User::factory()->create();

        Topic::refreshLatestTopics();

        $response = $this->actingAs($other_user)
            ->get(route('users.topics', $topic->create_user))
            ->assertDontSeeText($topic->name);

        Topic::refreshLatestTopics();

        $response = $this
            ->get(route('users.topics', $topic->create_user))
            ->assertDontSeeText($topic->name);
    }

}
