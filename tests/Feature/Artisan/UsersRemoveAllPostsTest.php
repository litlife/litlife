<?php

namespace Tests\Feature\Artisan;

use App\Blog;
use App\Comment;
use App\Post;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UsersRemoveAllPostsTest extends TestCase
{
	public function testWallPosts()
	{
		$blog = Blog::factory()->create();

		$user = $blog->create_user;

		Artisan::call('user:remove_all_posts', ['user_id' => $user->id]);

		$blog->refresh();

		$this->assertTrue($blog->trashed());
	}

	public function testComments()
	{
		$comment = Comment::factory()->create();

		$user = $comment->create_user;

		Artisan::call('user:remove_all_posts', ['user_id' => $user->id]);

		$comment->refresh();

		$this->assertTrue($comment->trashed());
	}

	public function testPosts()
	{
		$post = Post::factory()->create();

		$user = $post->create_user;

		Artisan::call('user:remove_all_posts', ['user_id' => $user->id]);

		$post->refresh();

		$this->assertTrue($post->trashed());
	}
}
