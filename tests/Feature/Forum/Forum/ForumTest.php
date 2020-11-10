<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use Tests\TestCase;

class ForumTest extends TestCase
{
	public function testFulltextSearch()
	{
		Forum::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	/*
		public function testSeeLabelsIfIdeaForum()
		{
			$admin = User::factory()->create();
			$admin->group->forum_edit_forum = true;
			$admin->group->manipulate_topic = true;
			$admin->group->edit_forum_self_topic = true;
			$admin->group->edit_forum_other_user_topic = true;
			$admin->push();

			$forum = Forum::factory()->create();

			$topic = Topic::factory()->create(['forum_id' => $forum->id]);

			$post = Post::factory()->create(['topic_id' => $topic->id]);

			$this->assertFalse($forum->isIdeaForum());

			$response = $this->actingAs($admin)
				->get(route('forums.show', ['forum' => $forum->id]))
				->assertOk()
				->assertDontSeeText(__('topic.labels.IdeaImplemented'))
				->assertDontSeeText(__('topic.labels.IdeaOnReview'))
				->assertDontSeeText(__('topic.labels.IdeaInProgress'))
				->assertDontSeeText(__('topic.labels.IdeaRejected'));

			$forum->is_idea_forum = true;
			$forum->save();

			$this->assertTrue($forum->isIdeaForum());

			$response = $this->actingAs($admin)
				->get(route('forums.show', ['forum' => $forum->id]))
				->assertOk()
				->assertSeeText(__('topic.labels.IdeaImplemented'))
				->assertSeeText(__('topic.labels.IdeaOnReview'))
				->assertSeeText(__('topic.labels.IdeaInProgress'))
				->assertSeeText(__('topic.labels.IdeaRejected'));
		}
		*/

}
