<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Forum;
use App\Post;
use App\User;
use Tests\TestCase;

class AuthorForumTest extends TestCase
{
	public function testObjIdDefaultIsNull()
	{
		$forum = factory(Forum::class)
			->create();

		$forum->refresh();

		$this->assertNull($forum->obj_type);
		$this->assertNull($forum->obj_id);
	}

	public function testForumAutoCreate()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('authors.forum', $author))
			->assertOk();

		$author->refresh();

		$this->assertNotNull($author->forum_id);

		$forum = Forum::findOrFail($author->forum_id);

		$this->assertEquals('author', $forum->obj_type);
		$this->assertEquals($author->id, $forum->obj_id);

		$this->actingAs($user)
			->get(route('authors.forum', $author))
			->assertOk();

		$author->refresh();
		$forum->refresh();

		$this->assertEquals($author->id, $forum->forumable->id);
	}

	public function testShowTopic()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create();

		$user = factory(User::class)
			->create();

		$post = factory(Post::class)->create();

		$forum = $post->topic->forum;
		$forum->obj_type = 'author';
		$forum->obj_id = $author->id;
		$forum->save();

		$this->assertEquals($author->id, $forum->forumable->id);

		$this->actingAs($user)
			->get(route('topics.show', $post->topic))
			->assertOk()
			->assertSeeText($post->text);
	}
}
