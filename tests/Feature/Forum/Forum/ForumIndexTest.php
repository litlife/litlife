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
		$forum = Forum::factory()->create();

		$forum2 = Forum::factory()->create(['forum_group_id' => $forum->forum_group_id]);

		$response = $this->get(route('forums.index'))
			->assertOk();
	}

	public function testIndexForAdmin()
	{
		$user = User::factory()->admin()->create();

		$response = $this->actingAs($user)
			->get(route('forums.index'))
			->assertOk();
	}

	public function testViewPrivateForumOnForumsIndex()
	{
		$post = Post::factory()->create();

		$user = $post->create_user;

		$forum_group = ForumGroup::factory()->create();

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

		$other_user = User::factory()->create();

		$response = $this->actingAs($other_user)
			->get(route('forums.index'))
			->assertDontSeeText($forum->name);

		$response = $this
			->get(route('forums.index'))
			->assertDontSeeText($forum->name);
	}
}
