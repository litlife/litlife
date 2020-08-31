<?php

namespace Tests\Browser;

use App\Post;
use App\Topic;
use Tests\DuskTestCase;

class AchievementTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testOnForum()
	{
		$this->browse(function ($browser) {

			$topic = factory(Topic::class)->create();

			$post = factory(Post::class)
				->states('create_user_with_achievement')
				->create(['topic_id' => $topic->id]);

			$post2 = factory(Post::class)
				->states('create_user_with_achievement')
				->create(['topic_id' => $topic->id]);

			$post3 = factory(Post::class)
				->states('create_user_with_achievement')
				->create(['topic_id' => $topic->id]);

			$post4 = factory(Post::class)
				->states('create_user_with_achievement')
				->create(['topic_id' => $topic->id]);

			$browser->resize(1000, 1000)
				->visit(route('topics.show', $topic))
				->with('.item[data-id="' . $post->id . '"]', function ($block) use ($post) {
					$block->assertVisible('.user-info')
						->assertVisible('.user-info .achievement-badge')
						->with('.achievement-badge[data-user-achievement-id="' .
							$post->create_user->user_achievements->first()->id . '"]', function ($badge) use ($post) {
							$badge->assertVisible('img');
						});
				});

			$browser->resize(1000, 1000)
				->visit(route('topics.show', $topic))
				->with('.item[data-id="' . $post2->id . '"]', function ($block) use ($post2) {
					$block->assertVisible('.user-info')
						->assertVisible('.user-info .achievement-badge')
						->with('.achievement-badge[data-user-achievement-id="' .
							$post2->create_user->user_achievements->first()->id . '"]', function ($badge) use ($post2) {
							$badge->assertVisible('img');
						});
				});

			$browser->resize(1000, 1000)
				->visit(route('topics.show', $topic))
				->with('.item[data-id="' . $post3->id . '"]', function ($block) use ($post3) {
					$block->assertVisible('.user-info')
						->assertVisible('.user-info .achievement-badge')
						->with('.achievement-badge[data-user-achievement-id="' .
							$post3->create_user->user_achievements->first()->id . '"]', function ($badge) use ($post3) {
							$badge->assertVisible('img');
						});
				});

			$browser->resize(1000, 1000)
				->visit(route('topics.show', $topic))
				->with('.item[data-id="' . $post4->id . '"]', function ($block) use ($post4) {
					$block->assertVisible('.user-info')
						->assertVisible('.user-info .achievement-badge')
						->with('.achievement-badge[data-user-achievement-id="' .
							$post4->create_user->user_achievements->first()->id . '"]', function ($badge) use ($post4) {
							$badge->assertVisible('img');
						});
				});

		});
	}
}

