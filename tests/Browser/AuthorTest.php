<?php

namespace Tests\Browser;

use App\Author;
use App\Book;
use App\BookVote;
use App\Comment;
use App\Manager;
use App\User;
use Tests\DuskTestCase;

class AuthorTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testSendRequest()
	{
		$this->browse(function ($user_browser) {

			$user = factory(User::class)->create();
			$user->group->author_editor_request = true;
			$user->push();

			$author = factory(Author::class)->create();

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('authors.show', $author))
				->assertSeeLink(__('author.iam_the_author'))
				->clickLink(__('author.iam_the_author'))
				->type('comment', $this->faker->realText(200))
				->press(__('manager.send_request'))
				->assertSee(__('manager.request_has_been_sent'))
				->visit(route('authors.show', $author))
				->assertSee(__('manager.request_on_review'));

			$manager = $author->managers->first();

			$this->assertTrue($manager->isSentForReview());
		});
	}

	public function testDeleteRequest()
	{
		$this->browse(function ($user_browser) {

			$manager = factory(Manager::class)
				->states('on_review')
				->create();

			$user = $manager->user;
			$author = $manager->manageable;
			$user->group->author_editor_request = true;
			$user->push();

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('authors.show', $author))
				->assertSee(__('common.delete'))
				->clickLink(__('common.delete'))
				->assertDontSee(__('manager.request_on_review'))
				->assertSeeLink(__('author.iam_the_author'));

			$manager->refresh();

			$this->assertTrue($manager->trashed());
		});
	}

	public function testCheckManager()
	{
		$this->browse(function ($admin_browser) {

			$admin_user = factory(User::class)->create();
			$admin_user->group->author_editor_check = true;
			$admin_user->group->moderator_add_remove = true;
			$admin_user->push();

			$manager = factory(Manager::class)
				->states('on_review')
				->create();

			$user = $manager->user;

			$author = factory(Author::class)->create();

			$admin_browser->resize(1000, 1000)
				->loginAs($admin_user)
				->visit(route('managers.on_check', ['order' => 'latest']))
				->assertPresent('.item[data-manager-id="' . $manager->id . '"]')
				->with('.item[data-manager-id="' . $manager->id . '"]', function ($div_manager) {
					$div_manager->click('.btn-start-review')
						->waitUntilMissing('.btn-start-review')
						->assertVisible('.btn-approve')
						->assertVisible('.btn-decline')
						->assertVisible('.btn-stop-review')
						->click('.btn-approve')
						->waitForText(__('manager.request_approved'))
						->assertSee(__('manager.request_approved'));
				});

			$manager->refresh();

			$this->assertTrue($manager->isAccepted());
		});
	}

	public function testDeleteManager()
	{
		$this->browse(function ($admin_browser) {

			$admin_user = factory(User::class)->create();
			$admin_user->group->author_editor_check = true;
			$admin_user->group->moderator_add_remove = true;
			$admin_user->push();

			$manager = factory(Manager::class)
				->states('accepted')
				->create();

			$user = $manager->user;
			$author = $manager->manageable;

			$admin_browser
				->loginAs($admin_user)
				->visit(route('authors.managers', $author))
				->whenAvailable('.item[data-id="' . $manager->id . '"]', function ($item) {
					$item->assertVisible('.dropdown-toggle')
						->click('.dropdown-toggle')
						->whenAvailable('.dropdown-menu.show', function ($menu) {
							$menu->click('.delete');
						});
				});

			$manager->refresh();

			$this->assertTrue($manager->trashed());
		});
	}

	public function testSeeNickIfManagerAccepted()
	{
		$this->browse(function ($browser) {

			$manager = factory(Manager::class)
				->states('accepted')
				->create();

			$user = $manager->user;
			$author = $manager->manageable;

			$browser->visit(route('authors.show', $author))
				->with('main', function ($main) use ($user) {
					$main->assertSee($user->nick);
				});
		});
	}

	public function testSeeNickIfManagerSentForReview()
	{
		$this->browse(function ($browser) {

			$manager = factory(Manager::class)
				->states('on_review')
				->create();

			$user = $manager->user;
			$author = $manager->manageable;

			$browser->visit(route('authors.show', $author))
				->with('main', function ($main) use ($user) {
					$main->assertDontSee($user->nick);
				});
		});
	}

	public function testPageAndTabs()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->create();

			$book = factory(Book::class)
				->states('with_writer')
				->create();

			$author = $book->writers()->get()->first();

			// send request
			$browser->resize(1000, 2000)
				->visit(route('authors.show', $author))
				->assertSee($author->name)
				->click('[href="#books"]')
				->waitForText($book->title)
				->click('[href="#comments"]')
				->waitForText(__('comment.nothing_found'), 15)
				->click('[href="#forum"]')
				->waitForText(__('topic.nothing_found'), 15)
				->click('[href="#votes"]')
				->waitForText(__('book_vote.nothing_found'), 15);
		});

	}

	public function testPageAndVotesTabs()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->create();

			$book = factory(Book::class)
				->states('with_writer')
				->create();

			$book_vote = factory(BookVote::class)
				->make();

			$book->votes()->save($book_vote);

			$user = $book_vote->create_user;

			$author = $book->writers()->get()->first();

			// send request
			$browser->resize(1000, 2000)
				->visit(route('authors.show', $author))
				->assertSee($author->name)
				->click('[href="#votes"]')
				->waitForText($user->nick)
				->with('#votes', function ($votes) use ($book_vote) {
					$votes->assertSee(__('common.vote') . ': ' . $book_vote->vote);
				});
		});
	}

	public function testForumTab()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->add_forum_topic = true;
			$user->push();

			$author = factory(Author::class)
				->create();

			$title = $this->faker->realText(100);
			$description = $this->faker->realText(100);
			$text = $this->faker->realText(100);

			$browser->resize(1000, 2000)
				->loginAs($user)
				->visit(route('authors.show', $author))
				->assertSee($author->name)
				->click('[href="#forum"]')
				->waitForText(__('topic.nothing_found'))
				->assertSee(__('topic.create'))
				->clickLink(__('topic.create'))
				->type('name', $title)
				->type('description', $description);

			$browser->driver->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("' . $text . '");');
			$browser->press(__('common.create'));

			$browser->resize(1000, 2000)
				->visit(route('authors.show', $author))
				->click('[href="#forum"]')
				->waitForText($title)
				->assertSee($title);
		});
	}

	public function testCommentsTab()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)->create();
			$user->group->add_comment = true;
			$user->push();

			$book = factory(Book::class)
				->states('with_writer')
				->create();

			$comments = factory(Comment::class, 16)
				->create([
					'commentable_id' => $book->id,
					'commentable_type' => 'book'
				]);

			$author = $book->writers()->get()->first();

			$title = $this->faker->realText(100);
			$description = $this->faker->realText(100);
			$text = $this->faker->realText(100);

			$browser->resize(1000, 2000)
				->loginAs($user)
				->visit(route('authors.show', $author))
				->assertSee($author->name)
				->click('[href="#comments"]')
				->waitFor('.comments-search-container')
				->with('.comments-search-container', function ($container) use ($author, $book) {

					$container->with('.list', function ($list) use ($author, $book) {

						$comments = $book->comments()->latest()->limit(10)->get();

						foreach ($comments as $comment) {
							$list->assertSee($comment->text);
						}

						$list->with('.pagination', function ($pagination) {
							$pagination->click('[rel=next]');
						});
					});

					//$container->waitFor('.list.loading-cap')
					$container->waitUntilMissing('.list.loading-cap')
						->assertMissing('.comments-search-container');
				});
		});
	}
}