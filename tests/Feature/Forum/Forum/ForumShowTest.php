<?php

namespace Tests\Feature\Forum\Forum;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\Like;
use App\Post;
use App\Topic;
use App\User;
use Tests\TestCase;

class ForumShowTest extends TestCase
{
	public function testPrivate()
	{
		$post = Post::factory()->create();

		$user = $post->create_user;

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$this->assertFalse($user->can('view', $forum));

		$forum->users_with_access()->sync([$user->id]);
		$forum->refresh();

		$this->assertTrue($user->can('view', $forum));

		$response = $this->actingAs($user)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeText($forum->name);

		$other_user = User::factory()->create();

		$response = $this->actingAs($other_user)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertForbidden();

		$response = $this->get(route('forums.show', ['forum' => $forum->id]))
			->assertForbidden();
	}

	public function testOrderTopicsBasedOnFixPostLikes()
	{
		$admin = User::factory()->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = Forum::factory()->create([
				'is_idea_forum' => true
			]);

		$this->assertTrue($forum->isIdeaForum());

		$topic = Topic::factory()->create(['forum_id' => $forum->id]);
		$post = Post::factory()->create(['topic_id' => $topic->id]);
		$topic2 = Topic::factory()->create(['forum_id' => $forum->id]);
		$post2 = Post::factory()->create(['topic_id' => $topic2->id]);

		$post->fix();
		$post2->fix();

		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(1, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic2->name]);

		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);
		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);

		$this->assertEquals(2, $post2->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic2->name, $topic->name]);

		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);
		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(3, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic2->name]);

	}

	public function testOrderForIdeaForumMostLikes()
	{
		$admin = User::factory()->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = Forum::factory()->create(['is_idea_forum' => true]);

		$this->assertTrue($forum->isIdeaForum());

		$topic = Topic::factory()->create(['forum_id' => $forum->id]);
		$post = Post::factory()->create(['topic_id' => $topic->id]);
		$topic2 = Topic::factory()->create(['forum_id' => $forum->id]);
		$post2 = Post::factory()->create(['topic_id' => $topic2->id]);
		$topic3 = Topic::factory()->create(['forum_id' => $forum->id]);
		$post3 = Post::factory()->create(['topic_id' => $topic3->id]);

		$post->fix();
		$post2->fix();
		$post3->fix();

		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(1, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic3->name, $topic2->name]);

		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);
		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);

		$this->assertEquals(2, $post2->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic2->name, $topic->name, $topic3->name]);

		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);
		Like::factory()->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(3, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic2->name, $topic3->name]);
	}

	public function testOrderForIdeaForumIdeaOnReviewIdeaInProgressFirst()
	{
		$admin = User::factory()->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = Forum::factory()->create(['is_idea_forum' => true]);

		$this->assertTrue($forum->isIdeaForum());

		$topic = Topic::factory()->create(['forum_id' => $forum->id]);
		$post = Post::factory()->create(['topic_id' => $topic->id, 'like_count' => '10']);
		$topic2 = Topic::factory()->create(['forum_id' => $forum->id]);
		$post2 = Post::factory()->create(['topic_id' => $topic2->id, 'like_count' => '20']);
		$topic3 = Topic::factory()->create(['forum_id' => $forum->id]);
		$post3 = Post::factory()->create(['topic_id' => $topic3->id, 'like_count' => '30']);
		$topic4 = Topic::factory()->create(['forum_id' => $forum->id]);
		$post4 = Post::factory()->create(['topic_id' => $topic4->id, 'like_count' => '40']);

		$post->fix();
		$post2->fix();
		$post3->fix();
		$post4->fix();

		$topic->label = TopicLabelEnum::IdeaImplemented;
		$topic->save();

		$topic2->label = TopicLabelEnum::IdeaOnReview;
		$topic2->save();

		$topic3->label = TopicLabelEnum::IdeaRejected;
		$topic3->save();

		$topic4->label = TopicLabelEnum::IdeaInProgress;
		$topic4->save();

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([
				$topic4->name,
				$topic2->name,
				$topic3->name,
				$topic->name
			]);

		$topic->label = TopicLabelEnum::IdeaInProgress;
		$topic->save();

		$topic2->label = TopicLabelEnum::IdeaOnReview;
		$topic2->save();

		$topic3->label = TopicLabelEnum::IdeaOnReview;
		$topic3->save();

		$topic4->label = TopicLabelEnum::IdeaInProgress;
		$topic4->save();

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([
				$topic4->name,
				$topic3->name,
				$topic2->name,
				$topic->name
			]);

		$topic->label = TopicLabelEnum::IdeaRejected;
		$topic->save();

		$topic2->label = TopicLabelEnum::IdeaImplemented;
		$topic2->save();

		$topic3->label = TopicLabelEnum::IdeaRejected;
		$topic3->save();

		$topic4->label = TopicLabelEnum::IdeaImplemented;
		$topic4->save();

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([
				$topic4->name,
				$topic3->name,
				$topic2->name,
				$topic->name
			]);
	}

	public function testViewForumsIfPostDeleted()
	{
		$post = Post::factory()->with_forum_group()->create();

		$topic = $post->topic;
		$forum = $topic->forum;
		$group = $forum->group;

		$this->assertNotNull($group);
		$this->assertEquals(1, $topic->post_count);
		$this->assertEquals($post->id, $forum->last_post->id);

		$this->get(route('forums.index'))
			->assertOk()
			->assertSeeText($topic->name);

		$post->delete();
		$topic->refresh();
		$forum->refresh();

		$this->assertEquals(0, $topic->post_count);
		$this->assertNull($forum->last_post);

		$this->get(route('forums.index'))
			->assertOk();

		$this->get(route('forums.show', ['forum' => $forum]))
			->assertOk();
	}

}
