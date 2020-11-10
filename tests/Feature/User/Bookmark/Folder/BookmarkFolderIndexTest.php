<?php

namespace Tests\Feature\User\Bookmark\Folder;

use App\BookmarkFolder;
use App\User;
use Tests\TestCase;

class BookmarkFolderIndexTest extends TestCase
{
	public function testList()
	{
		$user = User::factory()->create();

		$folder = BookmarkFolder::factory()->create(['create_user_id' => $user]);

		$folder2 = BookmarkFolder::factory()->create(['create_user_id' => $user]);

		$this->actingAs($user)
			->get(route('bookmark_folders.list'))
			->assertOk()
			->assertSeeText($folder->title)
			->assertSeeText($folder2->title);
	}
}
