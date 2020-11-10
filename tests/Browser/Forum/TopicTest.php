<?php

namespace Tests\Browser\Forum;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\Post;
use App\Topic;
use App\User;
use Tests\DuskTestCase;

class TopicTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testSeeLabelsIfIdeaForum()
	{
		$this->browse(function ($user_browser) {

			$admin = User::factory()->create();
			$admin->group->forum_edit_forum = true;
			$admin->group->manipulate_topic = true;
			$admin->group->edit_forum_self_topic = true;
			$admin->group->edit_forum_other_user_topic = true;
			$admin->push();

			$forum = Forum::factory()->create();

			$topic = Topic::factory()->idea_implemented()->create();

			$topic2 = Topic::factory()->idea_on_review()->create();

			$topic3 = Topic::factory()->idea_rejected()->create();

			$topic4 = Topic::factory()->idea_in_progress()->create();

			$post = Post::factory()->create(['topic_id' => $topic->id]);

			$this->assertFalse($forum->isIdeaForum());

			$user_browser->resize(1000, 2000)
				->loginAs($admin)
				->visit(route('forums.show', ['forum' => $forum->id]))
				->whenAvailable('.forum-container', function ($container) {
					$container->assertDontSee(__('topic.labels.IdeaImplemented'))
						->assertDontSee(__('topic.labels.IdeaOnReview'))
						->assertDontSee(__('topic.labels.IdeaInProgress'))
						->assertDontSee(__('topic.labels.IdeaRejected'));
				});

			$forum->is_idea_forum = true;
			$forum->save();

			$this->assertTrue($forum->isIdeaForum());

			$user_browser->resize(1000, 2000)
				->loginAs($admin)
				->visit(route('forums.show', ['forum' => $forum->id]))
				->whenAvailable('.forum-container', function ($container) {
					$container->assertSee(__('topic.labels.IdeaImplemented'))
						->assertSee(__('topic.labels.IdeaOnReview'))
						->assertSee(__('topic.labels.IdeaInProgress'))
						->assertSee(__('topic.labels.IdeaRejected'));
				});
		});
	}

	public function testViewLabelIfIdeaForum()
	{
		$this->browse(function ($user_browser) {

			$admin = User::factory()->create();
			$admin->push();

			$forum = Forum::factory()->create();
			$forum->is_idea_forum = true;
			$forum->save();

			$this->assertTrue($forum->fresh()->isIdeaForum());

			$topic = Topic::factory()->create(['forum_id' => $forum->id]);
			$topic->label = TopicLabelEnum::IdeaImplemented;
			$topic->save();

			$user_browser->resize(1000, 2000)
				->loginAs($admin)
				->visit(route('forums.show', ['forum' => $forum->id]))
				->whenAvailable('.forum-container', function ($container) use ($topic) {
					$container->assertSee($topic->name)
						->assertSee(__('topic.labels.IdeaImplemented'));
				});

			$forum->is_idea_forum = false;
			$forum->save();

			$user_browser->resize(1000, 2000)
				->loginAs($admin)
				->visit(route('forums.show', ['forum' => $forum->id]))
				->whenAvailable('.forum-container', function ($container) use ($topic) {
					$container->assertSee($topic->name)
						->assertDontSee(__('topic.labels.IdeaImplemented'));
				});
		});
	}

	public function testSubscribeToggle()
	{
		$this->browse(function ($user_browser) {

			$user = User::factory()->create();

			$topic = Topic::factory()->create();

			$user_browser->resize(1000, 2000)
				->loginAs($user)
				->visit(route('topics.show', ['topic' => $topic]))
				->whenAvailable('.btn-bell-toggle', function ($btn) {
					$btn->assertSee(__('topic.notify_on_new_posts'))
						->assertDontSee(__('topic.disable_notify_on_new_posts'))
						->click('[data-status="empty"]')
						//->waitFor('[data-status="wait"]')
						//->waitUntilMissing('[data-status="wait"]')
						->waitFor('[data-status="filled"]')
						->assertSee(__('topic.disable_notify_on_new_posts'))
						->assertDontSee(__('topic.notify_on_new_posts'));
				});

			$this->assertEquals($user->id, $topic->subscribed_users()
				->where('user_id', $user->id)
				->first()
				->id);

			$user_browser->whenAvailable('.btn-bell-toggle', function ($btn) {
				$btn->assertSee(__('topic.disable_notify_on_new_posts'))
					->assertDontSee(__('topic.notify_on_new_posts'))
					->click('[data-status="filled"]')
					//->waitFor('[data-status="wait"]')
					//->waitUntilMissing('[data-status="wait"]')
					->waitFor('[data-status="empty"]');
			});

			$this->assertNull($topic->subscribed_users()
				->where('user_id', $user->id)
				->first());
		});
	}
}
