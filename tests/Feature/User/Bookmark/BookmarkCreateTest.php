<?php

namespace Tests\Feature\User\Bookmark;

use App\BookmarkFolder;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookmarkCreateTest extends TestCase
{
	public function testStoreHttp()
	{
		$user = User::factory()->create();

		$title = $this->faker->realText(100);
		$url = '/test?test=test';

		$response = $this->actingAs($user)
			->post(route('bookmarks.store'), [
				'title' => $title,
				'url' => $url
			])
			->assertStatus(201);

		$bookmark = $user->bookmarks()->first();

		$response->assertJson($bookmark->toArray());

		$this->assertEquals($title, $bookmark->title);
		$this->assertEquals($url, $bookmark->url);
	}

	public function testStoreInFolderHttp()
	{
		$user = User::factory()->create();

		$folder = BookmarkFolder::factory()->create(['create_user_id' => $user]);

		$title = $this->faker->realText(100);
		$url = '/test?test=test';

		$response = $this->actingAs($user)
			->post(route('bookmarks.store'), [
				'title' => $title,
				'url' => $url,
				'folder' => $folder->id
			])
			->assertStatus(201);

		$bookmark = $folder->bookmarks()->first();

		$response->assertJson($bookmark->toArray());

		$this->assertEquals($title, $bookmark->title);
		$this->assertEquals($url, $bookmark->url);
	}

	public function testStoreInFolderOtherUserNotFoundErrorHttp()
	{
		$user = User::factory()->create();

		$folder = BookmarkFolder::factory()->create();

		$title = $this->faker->realText(100);
		$url = '/test?test=test';

		$response = $this->actingAs($user)
			->post(route('bookmarks.store'), [
				'title' => $title,
				'url' => $url,
				'folder' => $folder->id
			])
			->assertNotFound();
	}

	public function testTitleMaxLengthValidation()
	{
		$user = User::factory()->create();

		$title = Str::random(260);
		$url = '/test?test=test';

		$response = $this->actingAs($user)
			->post(route('bookmarks.store'), [
				'title' => $title,
				'url' => $url
			])
			->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrors(['title' => __('validation.max.string', ['attribute' => __('bookmark.title'), 'max' => 250])]);
	}
}
