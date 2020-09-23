<?php

namespace Tests\Feature\User\Bookmark;

use App\Bookmark;
use App\User;
use Tests\TestCase;

class BookmarkDeleteTest extends TestCase
{
	public function testDeleteHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create();

		$response = $this->actingAs($bookmark->create_user)
			->delete(route('bookmarks.destroy', ['bookmark' => $bookmark->id]))
			->assertStatus(200);

		$bookmark->refresh();

		$response->assertJson($bookmark->toArray());

		$this->assertTrue($bookmark->trashed());
	}

	public function testRestoreHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create();

		$bookmark->delete();

		$response = $this->actingAs($bookmark->create_user)
			->delete(route('bookmarks.destroy', ['bookmark' => $bookmark->id]))
			->assertStatus(200);

		$bookmark->refresh();

		$response->assertJson($bookmark->toArray());

		$this->assertFalse($bookmark->trashed());
	}

	public function testDeleteOtherUser()
	{
		$user = factory(User::class)
			->create();

		$bookmark = factory(Bookmark::class)
			->create();

		$response = $this->actingAs($user)
			->delete(route('bookmarks.destroy', ['bookmark' => $bookmark->id]))
			->assertForbidden();
	}
}
