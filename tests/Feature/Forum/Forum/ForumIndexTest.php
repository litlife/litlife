<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\ForumGroup;
use App\Post;
use App\User;
use App\UsersAccessToForum;
use Tests\TestCase;

class ForumIndexTest extends TestCase
{
	public function testIndex()
	{
		$forum = factory(Forum::class)
			->create();

		$forum2 = factory(Forum::class)
			->create(['forum_group_id' => $forum->forum_group_id]);

		$response = $this->get(route('forums.index'))
			->assertOk();
	}

	public function testIndexForAdmin()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->get(route('forums.index'))
			->assertOk();
	}

	public function testViewPrivateForumOnForumsIndex()
	{
		$post = factory(Post::class)
			->create();

		$user = $post->create_user;

		$forum_group = factory(ForumGroup::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->forum_group_id = $forum_group->id;
		$forum->save();

		$this->assertFalse($user->can('view', $forum));

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $user->id;
		$forum->user_access()->save($usersAccessToForum);
		$forum->refresh();

		$this->assertTrue($user->can('view', $forum));

		$response = $this->actingAs($user)
			->get(route('forums.index'))
			->assertSeeText($forum->name);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('forums.index'))
			->assertDontSeeText($forum->name);

		$response = $this
			->get(route('forums.index'))
			->assertDontSeeText($forum->name);
	}
}
