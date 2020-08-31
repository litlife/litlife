<?php

namespace Tests\Browser\Forum;

use App\Post;
use App\Topic;
use App\User;
use Faker\Factory as Faker;
use Tests\DuskTestCase;

class PostTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testGoToPost()
	{
		$this->browse(function ($user_browser) {

			$topic = factory(Topic::class)->create();

			$post = factory(Post::class)
				->create(['topic_id' => $topic->id])->fresh();

			$post2 = factory(Post::class)
				->create(['parent' => $post, 'topic_id' => $topic->id])->fresh();

			$post3 = factory(Post::class)
				->create(['parent' => $post2, 'topic_id' => $topic->id])->fresh();

			$post4 = factory(Post::class)
				->create(['parent' => $post3, 'topic_id' => $topic->id])->fresh();

			$post5 = factory(Post::class)
				->create(['parent' => $post4, 'topic_id' => $topic->id])->fresh();

			$this->assertEquals(4, $post5->level);

			$user_browser->resize(1000, 2000)
				->visit(route('posts.go_to', ['post' => $post5->id]))
				->whenAvailable('.item[data-id="' . $post5->id . '"]', function ($item) use ($post5) {
					$item->assertSee($post5->text);
				});
		});
	}

	public function testShowHideDescendants()
	{
		$this->browse(function ($user_browser) {

			$topic = factory(Topic::class)->create();

			$post = factory(Post::class)
				->create(['topic_id' => $topic->id])->fresh();

			$post2 = factory(Post::class)
				->create(['parent' => $post, 'topic_id' => $topic->id])->fresh();

			$user_browser->resize(1000, 2000)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->assertSee($post2->text)
				->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) use ($post2) {
					$item->assertVisible('.close_descendants')
						->click('.close_descendants')
						->waitFor('.open_descendants', 15)
						->assertVisible('.open_descendants')
						->assertDontSee($post2->text)
						->click('.open_descendants')
						->waitFor('.close_descendants', 15)
						->assertVisible('.close_descendants')
						->assertSee($post2->text);
				});
		});
	}

	public function testReply()
	{
		$this->browse(function ($user_browser) {

			$topic = factory(Topic::class)->create();

			$post0 = factory(Post::class)
				->create(['topic_id' => $topic->id])->fresh();

			$post = factory(Post::class)
				->create(['topic_id' => $topic->id])->fresh();

			$user_browser->resize(1000, 2000)
				->loginAs($post0->create_user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->assertVisible('.btn-reply')
						->click('.btn-reply')
						->waitFor('.sceditor-container');
				});

			$user_browser->driver->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("' . Faker::create()->text . '");');
			$user_browser->press(__('common.create'));

			$post = $topic->posts()->latest()->first();

			$user_browser->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) use ($post) {
				$item->assertSee($post->text);
			});
		});
	}

	public function testFixedPost()
	{
		$this->browse(function ($user_browser) {

			$topic = factory(Topic::class)->create();

			$post = factory(Post::class)
				->create(['topic_id' => $topic->id])->fresh();

			$post2 = factory(Post::class)
				->create(['topic_id' => $topic->id])->fresh();

			$post->fix();

			$user_browser->resize(1000, 2000)
				->loginAs($post->create_user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->click('.dropdown-toggle')
						->waitFor('.dropdown-menu.show')
						->waitFor('.get_link')
						->assertSee(__('common.link_to_message'))
						->click('.get_link');
				})
				->whenAvailable('.bootbox.modal.show', function ($dialog) use ($post) {
					$url = $dialog->text('textarea');
					$this->assertEquals(route('posts.go_to', ['post' => $post->id]), $url);
				});
		});
	}

	public function testSeeDropdownMenu()
	{
		$this->browse(function ($user_browser) {

			$post = factory(Post::class)
				->create()
				->fresh();

			$topic = $post->topic;

			$post->create_user->group->complain = true;
			$post->push();

			$user_browser->resize(1000, 2000)
				->loginAs($post->create_user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->assertVisible('.btn-group .dropdown-toggle');
				});

			$user_browser->resize(1000, 2000)
				->loginAs($post->create_user)
				->visit(route('home.latest_posts'))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->assertVisible('.btn-group .dropdown-toggle');
				});
		});
	}

	public function testEdit()
	{
		$this->browse(function ($user_browser) {

			$admin = factory(User::class)->states('admin')->create();

			$bb_code = 'text';

			$new_bb_code = uniqid();

			$post = factory(Post::class)
				->create(['bb_text' => $bb_code, 'create_user_id' => $admin->id]);

			$topic = $post->topic;

			$user_browser->resize(1000, 2000)
				->loginAs($admin)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {

					$item->with('.btn-group', function ($btn_group) {
						$btn_group->click('.dropdown-toggle')
							->waitFor('.dropdown-menu.show')
							->with('.dropdown-menu', function ($menu) {
								$menu->click('.btn-edit');
							});
					});

					$item->waitFor('.sceditor-container');
				});

			$user_browser->driver
				->executeScript('sceditor.instance($(\'.item[data-id="' . $post->id . '"]\').find(\'.sceditor\').first().get(0)).val("' . $new_bb_code . '").updateOriginal();');

			$user_browser->with('.item[data-id="' . $post->id . '"]', function ($item) use ($new_bb_code) {
				$item->assertSee(__('common.save'))
					->press(__('common.save'))
					->waitUntilMissing('form')
					->pause(500)
					->assertSee($new_bb_code);
			});
		});
	}

	public function testExpandButtonMissingAfterEdit()
	{
		$this->browse(function ($user_browser) {

			$admin = factory(User::class)->states('admin')->create();

			$long_text = 'test ' . implode("\r\n", array_fill(0, 50, ' ')) . ' test';

			$short_text = uniqid();

			$post = factory(Post::class)
				->create(['bb_text' => $long_text, 'create_user_id' => $admin->id]);

			$topic = $post->topic;

			$user_browser->resize(1000, 2000)
				->loginAs($admin)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {

					$item->assertVisible('.btn-expand');

					$item->with('.btn-group', function ($btn_group) {
						$btn_group->click('.dropdown-toggle')
							->waitFor('.dropdown-menu.show')
							->with('.dropdown-menu', function ($menu) {
								$menu->click('.btn-edit');
							});
					});

					$item->waitFor('.sceditor-container');
				});

			$user_browser->driver
				->executeScript('sceditor.instance($(\'.item[data-id="' . $post->id . '"]\').find(\'.sceditor\').first().get( 0 )).val("' . $short_text . '").updateOriginal();');

			$user_browser->with('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->press(__('common.save'))
					->waitUntilMissing('form')
					->assertMissing('.btn-expand');
			});
		});
	}

	public function testExpandAndCompressOnClickButton()
	{
		$this->browse(function ($user_browser) {

			$begin = uniqid();
			$end = uniqid();

			$bb = '[b]' . $begin . '[/b] ' . implode("\r\n", array_fill(0, 50, ' ')) . ' [i]' . $end . '[/i]';

			$post = factory(Post::class)
				->create(['bb_text' => $bb]);

			$topic = $post->topic;

			$user_browser->resize(1000, 2000)
				->loginAs($post->create_user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->with('.item[data-id="' . $post->id . '"]', function ($item) use ($begin, $end) {
					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertMissing("emphasis");
					});

					$item->assertMissing('.btn-compress')
						->assertVisible('.btn-expand')
						->click('.btn-expand')
						->assertMissing('.btn-expand')
						->assertVisible('.btn-compress');

					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertVisible("i");
					});

					$item->click('.btn-compress')
						->assertMissing('.btn-compress')
						->assertVisible('.btn-expand');

					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertMissing("emphasis");
					});
				});
		});
	}

	public function testExpandAndCompressOnClickInside()
	{
		$this->browse(function ($user_browser) {

			$begin = uniqid();
			$end = uniqid();

			$bb = '[b]' . $begin . '[/b] ' . implode("\r\n", array_fill(0, 50, ' ')) . ' [i]' . $end . '[/i]';

			$post = factory(Post::class)
				->create(['bb_text' => $bb]);

			$topic = $post->topic;

			$user_browser->resize(1000, 2000)
				->loginAs($post->create_user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->with('.item[data-id="' . $post->id . '"]', function ($item) use ($begin, $end) {
					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertMissing("emphasis");
					});

					// click inside
					$item->assertMissing('.btn-compress')
						->assertVisible('.btn-expand')
						->click('strong')
						->assertMissing('.btn-expand')
						->assertVisible('.btn-compress');

					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertVisible("i");
					});

					// click inside again
					$item->click('strong')
						->assertMissing('.btn-expand')
						->assertVisible('.btn-compress');

					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertVisible("i");
					});

					// click compress button
					$item->click('.btn-compress')
						->assertMissing('.btn-compress')
						->assertVisible('.btn-expand');

					$item->with('.html_box', function ($html_box) {
						$html_box->assertVisible("strong")
							->assertMissing("emphasis");
					});
				});
		});
	}

	public function testDontShowExpandButtonIfTextOverflowOnClick()
	{
		$this->browse(function ($user_browser) {

			$post = factory(Post::class)
				->create(['bb_text' => '[b]text[/b]']);

			$topic = $post->topic;

			$user_browser->resize(1000, 2000)
				->loginAs($post->create_user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->with('.item[data-id="' . $post->id . '"]', function ($item) {

					$item->with('.html_box', function ($html_box) {
						$html_box->click('strong');
					});

					$item->assertMissing('.btn-expand')
						->assertMissing('.btn-compress');
				});
		});
	}

	public function testShowExpandButtonAfterReply()
	{
		$this->browse(function ($user_browser) {

			$user = factory(User::class)
				->create();

			$post = factory(Post::class)
				->create();

			$topic = $post->topic;

			$user_browser->resize(1000, 2000)
				->loginAs($user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->assertVisible('.btn-reply')
						->click('.btn-reply')
						->waitFor('.sceditor-container');
				});

			$text = implode('\n', array_fill(0, 100, 'текст'));

			$user_browser->driver->executeScript('sceditor.instance(document.getElementsByClassName("sceditor")[0]).insertText("' . $text . '");');
			$user_browser->press(__('common.create'));

			$user_browser->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->waitUntilMissing('.reply-box');
			});

			$reply = $topic->posts()->orderBy('id', 'desc')->first();

			$user_browser->whenAvailable('.item[data-id="' . $reply->id . '"]', function ($item) use ($reply) {
				$item->waitFor('.html_box')
					->assertVisible('.html_box');
			});

			$user_browser->with('.item[data-id="' . $reply->id . '"]', function ($item) use ($reply) {
				$item->waitFor('.btn-expand')
					->assertVisible('.btn-expand');
			});
		});
	}

	public function testRightPlaceOpenEditForm()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->forum_edit_self_post = true;
			$user->push();

			$post = factory(Post::class)
				->create();

			$topic = $post->topic;

			$browser->resize(1000, 2000)
				->loginAs($user)
				->visit(route('topics.show', ['topic' => $topic->id]))
				->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) {
					$item->assertVisible('.btn-reply')
						->click('.btn-reply')
						->waitFor('.sceditor-container');
				});

			$text = $this->faker->realText(200);

			$browser->driver->executeScript('sceditor.instance(document.getElementsByClassName("sceditor")[0]).insertText("' . $text . '");');
			$browser->press(__('common.create'));

			$browser->whenAvailable('.item[data-id="' . $post->id . '"]', function ($item) {
				$item->waitUntilMissing('.reply-box');
			});

			$reply = $topic->posts()->orderBy('id', 'desc')->first();

			$this->assertNotNull($reply);

			$browser
				->loginAs($user)
				->visit(route('posts.go_to', ['post' => $reply->id]))
				->whenAvailable('.item[data-id="' . $reply->id . '"]', function ($item) {
					$item->click('button.dropdown-toggle')
						->whenAvailable('.dropdown-menu', function ($menu) {
							$menu->clickLink(__('common.edit'));
						})
						->waitFor('form')
						->assertVisible('form');
				});
		});
	}
}
