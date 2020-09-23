<?php

namespace Tests\Feature\User\Bookmark;

use App\Bookmark;
use App\BookmarkFolder;
use App\User;
use Tests\TestCase;

class BookmarkIndexTest extends TestCase
{
	public function testIndex()
	{
		$user = factory(User::class)
			->create();

		$bookmark = factory(Bookmark::class)
			->create(['create_user_id' => $user->id]);

		$folder = factory(BookmarkFolder::class)
			->create(['create_user_id' => $user->id]);

		$this->actingAs($user)
			->get(route('users.bookmarks.index', ['user' => $user]))
			->assertOk()
			->assertViewHas('folders')
			->assertViewHas('bookmarks');
	}
}
