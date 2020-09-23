<?php

namespace Tests\Feature\User\Bookmark;

use App\Bookmark;
use App\BookmarkFolder;
use Tests\TestCase;

class BookmarkEditTest extends TestCase
{
	public function testTryEditUnauthorizedHttp()
	{
		$bookmark = factory(Bookmark::class)->create();

		$this->get(route('bookmarks.edit', $bookmark))
			->assertStatus(401);
	}

	public function testEditHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$this->actingAs($bookmark->create_user)
			->get(route('bookmarks.edit', $bookmark))
			->assertOk()
			->assertViewHas('bookmark', $bookmark)
			->assertViewHas('bookmarks_folders', $bookmark->create_user->bookmark_folders);
	}

	public function testUpdateHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$title = $this->faker->realText(100);

		$this->actingAs($bookmark->create_user)
			->patch(route('bookmarks.update', $bookmark), [
				'title' => $title
			])
			->assertRedirect()
			->assertSessionHas(['success' => __('common.data_saved')]);

		$bookmark->refresh();

		$this->assertEquals($title, $bookmark->title);
	}

	public function testUpdateChangeFolderHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$folder = factory(BookmarkFolder::class)
			->create(['create_user_id' => $bookmark->create_user_id]);

		$title = $this->faker->realText(100);

		$this->actingAs($bookmark->create_user)
			->patch(route('bookmarks.update', $bookmark), [
				'title' => $title,
				'folder_id' => $folder->id
			])
			->assertRedirect()
			->assertSessionHas(['success' => __('common.data_saved')]);

		$bookmark->refresh();

		$this->assertEquals(1, $folder->bookmarks()->count());
	}

	public function testUpdateChangeFolderToFolderOfOtherUserHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$folder = factory(BookmarkFolder::class)
			->create();

		$title = $this->faker->realText(100);

		$this->actingAs($bookmark->create_user)
			->patch(route('bookmarks.update', $bookmark), [
				'title' => $title,
				'folder_id' => $folder->id
			])
			->assertNotFound();
	}
}
