<?php

namespace Tests\Browser;

use App\CollectedBook;
use App\Collection;
use App\Enums\StatusEnum;
use App\User;
use Tests\DuskTestCase;

class CollectionTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testDetachBook()
	{
		$this->browse(function ($user_browser) {

			$user = User::factory()->admin()->create();

			$collected_book = CollectedBook::factory()->create();

			$collection = $collected_book->collection;
			$collection->status = StatusEnum::Accepted;
			$collection->who_can_add = 'everyone';
			$collection->save();
			$collection->refresh();

			$book = $collected_book->book;

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('collections.books', $collection))
				->whenAvailable('.books-search-container', function ($container) use ($book) {
					$container->with('[data-id="' . $book->id . '"]', function ($book_item) {
						$book_item->click('.dropdown-toggle')
							->whenAvailable('.dropdown-menu', function ($menu) {
								$menu->assertVisible('.detach')
									->click('.detach');
							});
					})
						->waitFor('.loading-cap[data-id="' . $book->id . '"]')
						->assertPresent('.loading-cap[data-id="' . $book->id . '"]');
				});
		});
	}

	public function testSubscribeNewCommentNotificationToggle()
	{
		$this->browse(function ($user_browser) {

			$user = User::factory()->admin()->create();

			$collection = Collection::factory()->accepted()->create();

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('collections.comments', $collection))
				->whenAvailable('.btn-bell-toggle', function ($btn) {
					$btn->assertSee(__('collection.notify_on_new_comments'))
						->assertDontSee(__('collection.disable_notify_on_new_comments'))
						->click('[data-status="empty"]')
						//->waitFor('[data-status="wait"]')
						//->waitUntilMissing('[data-status="wait"]')
						->waitFor('[data-status="filled"]')
						->assertSee(__('collection.disable_notify_on_new_comments'))
						->assertDontSee(__('collection.notify_on_new_comments'))
						->click('[data-status="filled"]')
						//->waitFor('[data-status="wait"]')
						//->waitUntilMissing('[data-status="wait"]')
						->waitFor('[data-status="empty"]');
				});
		});
	}

	public function testViewCollectionAccessLimited()
	{
		$this->browse(function ($user_browser) {

			$user = User::factory()->admin()->create();

			$collection = Collection::factory()->private()->create();

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.collections.created', $collection->create_user))
				->whenAvailable('.collection[data-id="' . $collection->id . '"]', function ($collection_block) {
					$collection_block->with('.card-title', function ($title) {
						$title->assertSee(__('collection.access_to_the_collection_is_limited'));
					});
				});
		});
	}

	public function testAddToFavorite()
	{
		$this->browse(function ($user_browser) {

			$collection = Collection::factory()->accepted()->create();

			$user = $collection->create_user;

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.collections.created', $user))
				->with('.collection[data-id="' . $collection->id . '"]', function ($collection_block) {
					$collection_block->with('.btn-favorite', function ($button) {
						$button->assertPresent('[data-status="empty"]')
							->assertPresent('[data-status="filled"]')
							->assertPresent('[data-status="wait"]')
							->assertVisible('[data-status="empty"]')
							->assertMissing('[data-status="filled"]')
							->with('[data-status="empty"] .count', function ($count) {
								$count->assertSee(0);
							});
					})
						->click('.btn-favorite')
						->assertVisible('[data-status="wait"]')
						->waitUntilMissing('[data-status="wait"]')
						->pause(100)
						->with('.btn-favorite', function ($button) {
							$button->assertMissing('[data-status="empty"]')
								->assertVisible('[data-status="filled"]')
								->with('[data-status="filled"] .count', function ($count) {
									$count->assertSee(1);
								});
						})
						->pause(500)
						->with('.btn-favorite', function ($button) {
							$button->assertMissing('[data-status="empty"]')
								->assertVisible('[data-status="filled"]')
								->with('[data-status="filled"] .count', function ($count) {
									$count->assertSee(1);
								});
						});
				});

			$collection->refresh();

			$this->assertEquals(1, $collection->added_to_favorites_users_count);

			$user_browser->click('.btn-favorite')
				->with('.btn-favorite', function ($button) {
					$button->waitFor('[data-status="empty"]')
						->assertVisible('[data-status="empty"]')
						->assertMissing('[data-status="filled"]')
						->with('[data-status="empty"] .count', function ($count) {
							$count->assertSee(0);
						});
				});
		});
	}

	public function testSeeCollectionsMenuAtSidebar()
	{
		$this->browse(function ($user_browser) {

			$user = User::factory()->create();
			$user->group->manage_collections = true;
			$user->push();

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('profile', $user))
				->with('#sidebar', function ($sidebar) {
					$sidebar->assertSee(__('sidebar.my_collections'));
				});

		});
	}
}
