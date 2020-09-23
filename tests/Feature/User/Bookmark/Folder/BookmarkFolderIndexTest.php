<?php

namespace Tests\Feature\User\Bookmark\Folder;

use App\BookmarkFolder;
use App\User;
use Tests\TestCase;

class BookmarkFolderIndexTest extends TestCase
{
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
