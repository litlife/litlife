<?php

namespace Tests\Browser;

use App\Bookmark;
use App\BookmarkFolder;
use App\User;
use Tests\DuskTestCase;

class BookmarkTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testCreate()
	{
		$this->browse(function ($user_browser) {

			//$admin_user = factory(User::class)->create();

			$user = factory(User::class)->create();

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('profile', $user))
				->assertVisible('#bookmarkAddButton')
				->assertMissing('#bookmarkRemoveButton')
				->click('#bookmarkAddButton')
				->whenAvailable('#bookmarkAddModal', function ($modal) {
					$modal->press(__('common.add'));
				})
				->waitUntilMissing('#bookmarkAddModal')
				->visit(route('profile', $user))
				->assertVisible('#bookmarkRemoveButton')
				->assertMissing('#bookmarkAddButton');

			$bookmarks = $user->bookmarks()->get();

			$this->assertEquals(1, $bookmarks->count());
		});
	}

	public function testCreateInsideFolder()
	{
		$this->browse(function ($user_browser) {

			//$admin_user = factory(User::class)->create();

			$bookmark_folder = factory(BookmarkFolder::class)->create();

			$user = $bookmark_folder->create_user;

			$title = $this->faker->realText(100);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('profile', $user))
				->waitFor('#bookmarkAddButton', 20)
				->assertVisible('#bookmarkAddButton')
				->assertMissing('#bookmarkRemoveButton')
				->click('#bookmarkAddButton')
				->whenAvailable('#bookmarkAddModal', function ($modal) use ($bookmark_folder, $title) {
					$modal->type('title', $title)
						->waitFor('select[name=folder] option')
						->select('folder', $bookmark_folder->id)
						->press(__('common.add'));
				})
				->waitUntilMissing('#bookmarkAddModal')
				->visit(route('profile', $user))
				->waitFor('#bookmarkRemoveButton', 30)
				->assertVisible('#bookmarkRemoveButton')
				->assertMissing('#bookmarkAddButton');

			$this->assertEquals(2, $user->bookmark_folders()->count());
			$this->assertEquals(1, $user->bookmarks()->count());
			$this->assertEquals(1, $user->bookmark_folders()->where('title', '!=', 'Несортированные')->first()->bookmark_count);
			$this->assertEquals($title, $user->bookmark_folders()->where('title', '!=', 'Несортированные')->first()->bookmarks()->first()->title);

		});
	}

	public function testCreateBookmarkFoler()
	{
		$this->browse(function ($user_browser) {

			//$admin_user = factory(User::class)->create();

			$user = factory(User::class)->create();

			$title = $this->faker->realText(100);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.bookmarks.index', $user))
				->with('#bookmarks', function ($bookmarks) use ($title) {
					$bookmarks->type('title', $title)
						->press(__('bookmark_folder.create'))
						->whenAvailable('.folders', function ($list) use ($title) {
							$list->assertSee($title);
						});
				});

		});
	}

	public function testEdit()
	{
		$this->browse(function ($user_browser) {

			$user = factory(User::class)->create();

			$bookmark_folder = factory(BookmarkFolder::class)->create([
				'create_user_id' => $user->id
			]);

			$bookmark_folder2 = factory(BookmarkFolder::class)->create([
				'create_user_id' => $user->id
			]);

			$bookmark = factory(Bookmark::class)->create([
				'create_user_id' => $user->id
			]);

			$this->assertEquals($user->id, $bookmark_folder->create_user_id);
			$this->assertEquals($user->id, $bookmark_folder2->create_user_id);
			$this->assertEquals($user->id, $bookmark->create_user_id);

			$title = $this->faker->realText(100);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('bookmarks.edit', $bookmark))
				->with('#main', function ($main) use ($title, $bookmark_folder) {
					$main->type('title', $title)
						->select('folder_id', $bookmark_folder->id)
						->press(__('common.save'))
						->assertSee(__('common.data_saved'));
				});


			$this->assertEquals($title, $bookmark->fresh()->title);
			$this->assertEquals(1, $bookmark_folder->fresh()->bookmark_count);
			$this->assertEquals(0, $bookmark_folder2->fresh()->bookmark_count);

			$title = $this->faker->realText(100);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('bookmarks.edit', $bookmark))
				->with('#main', function ($main) use ($title, $bookmark_folder2) {
					$main->type('title', $title)
						->select('folder_id', $bookmark_folder2->id)
						->press(__('common.save'))
						->assertSee(__('common.data_saved'));
				});


			$this->assertEquals($title, $bookmark->fresh()->title);
			$this->assertEquals(0, $bookmark_folder->fresh()->bookmark_count);
			$this->assertEquals(1, $bookmark_folder2->fresh()->bookmark_count);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('bookmarks.edit', $bookmark))
				->with('#main', function ($main) use ($title) {
					$main->type('title', $title)
						->select('folder_id', '')
						->press(__('common.save'))
						->assertSee(__('common.data_saved'));
				});


			$this->assertEquals(0, $bookmark_folder->fresh()->bookmark_count);
			$this->assertEquals(0, $bookmark_folder2->fresh()->bookmark_count);

		});
	}

	public function testFolderSavePosition()
	{
		$this->browse(function ($user_browser) {

			$user = factory(User::class)->create();

			$auto_created_bookmark_folder = $user->bookmark_folders()->first();

			$bookmark_folder = factory(BookmarkFolder::class)->create([
				'create_user_id' => $user->id
			]);

			$bookmark_folder2 = factory(BookmarkFolder::class)->create([
				'create_user_id' => $user->id
			]);

			$order = [$bookmark_folder->id, $bookmark_folder2->id, $auto_created_bookmark_folder->id];

			$user->setting->bookmark_folder_order = $order;
			$user->setting->save();

			$this->assertEquals($order, $user->setting->bookmark_folder_order);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.bookmarks.index', $user))
				->whenAvailable('.folders', function ($folders) use ($bookmark_folder2) {
					$folders->dragUp('[data-id="' . $bookmark_folder2->id . '"] .handle', 500);
				})
				->waitForText(__('bookmark_folder.position_saved'));

			$this->assertEquals([$bookmark_folder2->id, $bookmark_folder->id, $auto_created_bookmark_folder->id],
				$user->setting->fresh()->bookmark_folder_order);

			$user_browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.bookmarks.index', $user))
				->whenAvailable('.folders', function ($folders) use ($bookmark_folder) {
					$folders->dragDown('[data-id="' . $bookmark_folder->id . '"] .handle', 500);
				})
				->waitForText(__('bookmark_folder.position_saved'));

			$this->assertEquals([$bookmark_folder2->id, $auto_created_bookmark_folder->id, $bookmark_folder->id],
				$user->setting->fresh()->bookmark_folder_order);
		});
	}
}
