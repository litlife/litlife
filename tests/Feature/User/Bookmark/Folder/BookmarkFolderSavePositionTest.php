<?php

namespace Tests\Feature\User\Bookmark\Folder;

use App\BookmarkFolder;
use App\User;
use Tests\TestCase;

class BookmarkFolderSavePositionTest extends TestCase
{
	public function testSavePosition()
	{
		$user = factory(User::class)
			->create();

		$bookmark_folder = factory(BookmarkFolder::class)
			->create(['create_user_id' => $user->id]);

		$bookmark_folder2 = factory(BookmarkFolder::class)
			->create(['create_user_id' => $user->id]);

		$this->actingAs($user)
			->post(route('users.bookmark_folders.save_position'), [
				'order' => [
					$bookmark_folder->id,
					$bookmark_folder2->id
				]
			])
			->assertOk()
			->assertSeeText(__('bookmark_folder.position_saved'));

		$user->refresh();

		$this->assertEquals([
			$bookmark_folder->id,
			$bookmark_folder2->id
		], $user->setting->bookmark_folder_order);

		$this->actingAs($user)
			->post(route('users.bookmark_folders.save_position'), [
				'order' => [
					$bookmark_folder2->id,
					$bookmark_folder->id
				]
			])
			->assertOk()
			->assertSeeText(__('bookmark_folder.position_saved'));

		$user->refresh();

		$this->assertEquals([
			$bookmark_folder2->id,
			$bookmark_folder->id
		], $user->setting->bookmark_folder_order);
	}

	public function testSavePositionIfDeleted()
	{
		$bookmark_folder = factory(BookmarkFolder::class)->create();
		$bookmark_folder->delete();

		$user = $bookmark_folder->create_user;

		$this->actingAs($user)
			->post(route('users.bookmark_folders.save_position'), [
				'order' => [$bookmark_folder->id]
			])
			->assertOk()
			->assertSeeText(__('bookmark_folder.position_saved'));
	}
}
