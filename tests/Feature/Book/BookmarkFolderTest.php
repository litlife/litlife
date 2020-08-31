<?php

namespace Tests\Feature\Book;

use App\BookmarkFolder;
use App\User;
use Tests\TestCase;

class BookmarkFolderTest extends TestCase
{
	public function testStoreHttp()
	{
		$user = factory(User::class)
			->create();

		$title = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->post(route('bookmark_folders.store'), [
				'title' => $title
			])
			->assertRedirect();

		$folder = $user->bookmark_folders()->where('title', $title)->first();

		$this->assertEquals($title, $folder->title);
	}

	public function testEditHttp()
	{
		$folder = factory(BookmarkFolder::class)
			->create();

		$this->actingAs($folder->create_user)
			->get(route('bookmark_folders.edit', $folder))
			->assertOk()
			->assertSeeText($folder->title);
	}

	public function testUpdateHttp()
	{
		$folder = factory(BookmarkFolder::class)
			->create();

		$title = $this->faker->realText(100);

		$this->actingAs($folder->create_user)
			->patch(route('bookmark_folders.update', $folder), [
				'title' => $title
			])
			->assertRedirect()
			->assertSessionHas(['success' => __('common.data_saved')]);

		$folder->refresh();

		$this->assertEquals($title, $folder->title);
	}

	public function testDeleteHttp()
	{
		$folder = factory(BookmarkFolder::class)
			->create();

		$response = $this->actingAs($folder->create_user)
			->delete(route('bookmark_folders.destroy', ['bookmark_folder' => $folder->id]))
			->assertStatus(200);

		$folder->refresh();

		$response->assertJson($folder->toArray());

		$this->assertSoftDeleted($folder);
	}

	public function testRestoreHttp()
	{
		$folder = factory(BookmarkFolder::class)
			->create();

		$folder->delete();

		$response = $this->actingAs($folder->create_user)
			->delete(route('bookmark_folders.destroy', ['bookmark_folder' => $folder->id]))
			->assertStatus(200);

		$folder->refresh();

		$response->assertJson($folder->toArray());

		$this->assertFalse($folder->trashed());
	}

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

	public function testList()
	{
		$user = factory(User::class)
			->create();

		$folder = factory(BookmarkFolder::class)
			->create(['create_user_id' => $user]);

		$folder2 = factory(BookmarkFolder::class)
			->create(['create_user_id' => $user]);

		$this->actingAs($user)
			->get(route('bookmark_folders.list'))
			->assertOk()
			->assertSeeText($folder->title)
			->assertSeeText($folder2->title);
	}
}
