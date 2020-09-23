<?php

namespace Tests\Feature\User\Bookmark\Folder;

use App\BookmarkFolder;
use Tests\TestCase;

class BookmarkFolderEditTest extends TestCase
{
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
}
