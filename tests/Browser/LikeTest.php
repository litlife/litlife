<?php

namespace Tests\Browser;

use App\Like;
use App\Post;
use App\User;
use Tests\DuskTestCase;

class LikeTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testAddLike()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->like_click = true;
			$user->push();

			$post = factory(Post::class)->create();
			/*
						$like = factory(Like::class)
							->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);
			*/
			$this->assertTrue($user->can('create', Like::class));

			$browser->loginAs($user)
				->visit(route('topics.show', $post->topic))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->click('.like')
						->waitFor('.like[aria-describedby]');
				});

			$popover_id = $browser->attribute('.item[data-id="' . $post->id . '"] .like', 'aria-describedby');

			$this->assertNotNull($popover_id);

			$browser->whenAvailable('#' . $popover_id, function ($popover) use ($user) {
				$popover->whenAvailable('.list-group', function ($list_group) use ($user) {
					$name = $list_group->attribute('.list-group-item', 'title');
					$this->assertEquals($user->userName, $name);
				}, 10);
			})->with('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->with('.like', function ($like) {
					$count = $like->text('.liked .counter');
					$this->assertEquals(1, $count);
				});

				//$this->assertEquals('true', $item->attribute('.like', 'data-loaded'));
			});
		});
	}

	public function testRemoveLike()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->like_click = true;
			$user->push();

			$post = factory(Post::class)->create();

			$like = factory(Like::class)
				->create([
					'likeable_type' => 'post',
					'likeable_id' => $post->id,
					'create_user_id' => $user->id
				]);

			$this->assertTrue($user->can('create', Like::class));

			$browser->loginAs($user)
				->visit(route('topics.show', $post->topic))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->click('.like')
						->waitUntilMissing('.like .liked')
						->assertVisible('.like .empty');

					$count = $item->text('.like .empty .counter');
					$this->assertEquals('', $count);
				});
		});
	}

	public function testMouseover()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->like_click = true;
			$user->push();

			$post = factory(Post::class)->create();

			$like = factory(Like::class)
				->create([
					'likeable_type' => 'post',
					'likeable_id' => $post->id,
					'create_user_id' => $user->id
				]);

			$this->assertTrue($user->can('create', Like::class));

			$browser->loginAs($user)
				->visit(route('topics.show', $post->topic))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->mouseover('.like');
				});

			$browser->pause(1500);

			$popover_id = $browser->attribute('.item[data-id="' . $post->id . '"] .like', 'aria-describedby');

			$this->assertNotNull($popover_id);

			$browser->whenAvailable('#' . $popover_id . '', function ($popover) use ($user) {
				$popover->whenAvailable('.list-group', function ($list_group) use ($user) {

					$name = $list_group->attribute('.list-group-item', 'title');
					$this->assertEquals($user->userName, $name);
				}, 10);
			})->with('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->assertSeeIn('.like .liked .counter', 1);
			});
		});
	}

	public function testDisappearAfterClickOutside()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->like_click = true;
			$user->push();

			$post = factory(Post::class)->create();

			$like = factory(Like::class)
				->create([
					'likeable_type' => 'post',
					'likeable_id' => $post->id,
					'create_user_id' => $user->id
				]);

			$this->assertTrue($user->can('create', Like::class));

			$browser->loginAs($user)
				->visit(route('topics.show', $post->topic))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->mouseover('.like');
				});

			$browser->pause(1500);

			$popover_id = $browser->attribute('.item[data-id="' . $post->id . '"] .like', 'aria-describedby');

			$this->assertNotNull($popover_id);

			$browser->whenAvailable('#' . $popover_id . '', function ($popover) use ($user) {
				$popover->whenAvailable('.list-group', function ($list_group) use ($user) {

					$name = $list_group->attribute('.list-group-item', 'title');
					$this->assertEquals($user->userName, $name);
				}, 10);
			})->with('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->assertSeeIn('.like .liked .counter', 1);
			});
		});
	}

	public function testGuestClick()
	{
		$this->browse(function ($browser) {

			$post = factory(Post::class)->create();

			$browser->visit(route('topics.show', $post->topic))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->click('.like')
						->waitFor('.like[aria-describedby]');
				});

			$popover_id = $browser->attribute('.item[data-id="' . $post->id . '"] .like', 'aria-describedby');

			$this->assertNotNull($popover_id);

			$browser->waitForText(__('error.401'));

			$browser->with('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->with('.like', function ($like) {
					$count = $like->text('.liked .counter');
					$this->assertEquals("", $count);

					$count = $like->text('.empty .counter');
					$this->assertEquals("", $count);

					$like->assertVisible('.empty')
						->assertMissing('.liked');
				});
			});
		});
	}
}
