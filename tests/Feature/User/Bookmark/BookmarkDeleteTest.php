<?php

namespace Tests\Feature\User\Bookmark;

use App\Bookmark;
use App\User;
use Tests\TestCase;

class BookmarkDeleteTest extends TestCase
{
	public function testDeleteHttp()
	{
		$bookmark = Bookmark::factory()->create();

		$response = $this->actingAs($bookmark->create_user)
			->delete(route('bookmarks.destroy', ['bookmark' => $bookmark->id]))
			->assertStatus(200);

		$bookmark->refresh();

		$response->assertJson($bookmark->toArray());

		$this->assertTrue($bookmark->trashed());
	}

	public function testRestoreHttp()
	{
		$bookmark = Bookmark::factory()->create();

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
		$user = User::factory()->create();

		$bookmark = Bookmark::factory()->create();

		$response = $this->actingAs($user)
			->delete(route('bookmarks.destroy', ['bookmark' => $bookmark->id]))
			->assertForbidden();
	}
}
