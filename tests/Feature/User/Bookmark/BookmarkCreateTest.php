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
		$user = factory(User::class)
			->create();

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
		$user = factory(User::class)
			->create();

		$folder = factory(BookmarkFolder::class)
			->create(['create_user_id' => $user]);

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
		$user = factory(User::class)
			->create();

		$folder = factory(BookmarkFolder::class)
			->create();

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
		$user = factory(User::class)
			->create();

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
