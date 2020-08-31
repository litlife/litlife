<?php

namespace Tests\Feature\Book;

use App\Bookmark;
use App\BookmarkFolder;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookmarkTest extends TestCase
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

	public function testCreate()
	{
		$bookmark = factory(Bookmark::class)
			->create([
				'url' => 'https://www.test.com:8080/test?test=test&key=value#test'
			]);

		$bookmark->refresh();

		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}

	public function testUrlAttribute()
	{
		$bookmark = new Bookmark;

		$bookmark->url = '?test=test';

		$this->assertEquals('/?test=test', $bookmark->url);

		$bookmark->url = 'https://www.test.com:8080/test?test=test&key=value#test';

		$this->assertEquals('/test?test=test&key=value', $bookmark->url);

		$bookmark->url = 'https://www.test.com/test?test=test&key=value#test';

		$this->assertEquals('/test?test=test&key=value', $bookmark->url);

		$bookmark->url = '/test?test=test&key=value#test';

		$this->assertEquals('/test?test=test&key=value', $bookmark->url);

		$bookmark->url = 'test?test=test&key=value#test';

		$this->assertEquals('/test?test=test&key=value', $bookmark->url);

		$bookmark->url = '/test?test=test&key=value#';

		$this->assertEquals('/test?test=test&key=value', $bookmark->url);

		$bookmark->url = '/test?test=test';

		$this->assertEquals('/test?test=test', $bookmark->url);

		$bookmark->url = '/test?';

		$this->assertEquals('/test', $bookmark->url);

		$bookmark->url = 'test?';

		$this->assertEquals('/test', $bookmark->url);

		$bookmark->url = '/?test=test';

		$this->assertEquals('/?test=test', $bookmark->url);

		$bookmark->url = '?';

		$this->assertEquals('/', $bookmark->url);

		$bookmark->url = '/test/?test=test';

		$this->assertEquals('/test/?test=test', $bookmark->url);

		$bookmark->url = '/test/test?test=test&page=1&#item';

		$this->assertEquals('/test/test?test=test&page=1', $bookmark->url);

		$bookmark->url = '/books?genre%5B%5D=130&genre%5B%5D=131&order=rating_avg_down&view=gallery';

		$this->assertEquals('/books?genre%5B%5D=130&genre%5B%5D=131&order=rating_avg_down&view=gallery', $bookmark->url);
	}

	public function testTryEditUnauthorizedHttp()
	{
		$bookmark = factory(Bookmark::class)->create();

		$this->get(route('bookmarks.edit', $bookmark))
			->assertStatus(401);
	}

	public function testEditHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$this->actingAs($bookmark->create_user)
			->get(route('bookmarks.edit', $bookmark))
			->assertOk()
			->assertViewHas('bookmark', $bookmark)
			->assertViewHas('bookmarks_folders', $bookmark->create_user->bookmark_folders);
	}

	public function testUpdateHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$title = $this->faker->realText(100);

		$this->actingAs($bookmark->create_user)
			->patch(route('bookmarks.update', $bookmark), [
				'title' => $title
			])
			->assertRedirect()
			->assertSessionHas(['success' => __('common.data_saved')]);

		$bookmark->refresh();

		$this->assertEquals($title, $bookmark->title);
	}

	public function testUpdateChangeFolderHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$folder = factory(BookmarkFolder::class)
			->create(['create_user_id' => $bookmark->create_user_id]);

		$title = $this->faker->realText(100);

		$this->actingAs($bookmark->create_user)
			->patch(route('bookmarks.update', $bookmark), [
				'title' => $title,
				'folder_id' => $folder->id
			])
			->assertRedirect()
			->assertSessionHas(['success' => __('common.data_saved')]);

		$bookmark->refresh();

		$this->assertEquals(1, $folder->bookmarks()->count());
	}

	public function testUpdateChangeFolderToFolderOfOtherUserHttp()
	{
		$bookmark = factory(Bookmark::class)
			->create(['url' => '/test']);

		$folder = factory(BookmarkFolder::class)
			->create();

		$title = $this->faker->realText(100);

		$this->actingAs($bookmark->create_user)
			->patch(route('bookmarks.update', $bookmark), [
				'title' => $title,
				'folder_id' => $folder->id
			])
			->assertNotFound();
	}

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

	public function testCurrentPageInBookmarkExists()
	{
		$url = '/test/test?test=test';

		$bookmark = factory(Bookmark::class)
			->create(['url' => $url]);

		$this->get($url);

		$this->assertEquals($url, $bookmark->url);

		$this->assertNotNull($bookmark->create_user->thisPageInBookmarks);
	}

	public function testCurrentPageInBookmarkNotExists()
	{
		$url = '/test/test';

		$bookmark = factory(Bookmark::class)
			->create(['url' => $url]);

		$this->get($url . '/test');

		$this->assertNull($bookmark->create_user->thisPageInBookmarks);
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
