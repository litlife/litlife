<?php

namespace Tests\Feature\User;

use App\Blog;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserShowTest extends TestCase
{
	public function testProfile()
	{
		$user = factory(User::class)->create()->fresh();

		$this->get(route('profile', compact('user')))
			->assertOk();
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testShowHttpWrongIdParam()
	{
		$this->get(route('profile', ['user' => Str::random(8)]))
			->assertNotFound();
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testShowDeletedProfileWithBlogMessagesFixed()
	{
		$user = factory(User::class)
			->create();

		$blog = factory(Blog::class)->create();
		$blog->create_user_id = $user->id;
		$blog->blog_user_id = $user->id;
		$blog->save();
		$blog->refresh();
		$blog->fix();

		$user->delete();

		$this->get(route('profile', compact('user')))
			->assertNotFound();
	}
}
