<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorPhoto;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorPhotoTest extends TestCase
{
	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		Storage::fake(config('filesystems.default'));
	}

	public function testStoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->create();
		$admin->group->author_edit = true;
		$admin->push();

		$author = factory(Author::class)
			->create();

		$file = UploadedFile::fake()->image('avatar.jpg');

		$response = $this->actingAs($admin)
			->post(route('authors.photos.store', ['author' => $author->id]), [
				'file' => $file,
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$author->refresh();
		$avatar = $author->avatar;

		$this->assertNotNull($avatar);
		$this->assertNotNull($avatar->exists());
		$this->assertEquals($avatar->size, $avatar->getSize());
		$this->assertEquals($avatar->width, $avatar->getRealWidth());
		$this->assertEquals($avatar->height, $avatar->getRealHeight());
		$this->assertEquals($avatar->create_user_id, $admin->id);

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('photo_set', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testDeleteHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->create();
		$admin->group->author_edit = true;
		$admin->push();

		$author = factory(Author::class)->create();

		$photo = new AuthorPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage(__DIR__ . '/../images/test.jpeg');
		$author->photos()->save($photo);

		$author->avatar()->associate($photo);
		$author->save();
		$author->refresh();

		$this->assertNotNull($author->avatar);

		$response = $this->actingAs($admin)
			->get(route('authors.photos.delete', ['author' => $author->id, 'id' => $author->avatar->id]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$author->refresh();

		$this->assertTrue($author->avatar()->withTrashed()->first()->trashed());

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('photo_remove', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testPolicy()
	{
		$admin = factory(User::class)->create();

		$author = factory(Author::class)->create();

		$author_photo = new AuthorPhoto;
		$author_photo->openImage(__DIR__ . '/../images/test.jpeg');
		$author->photos()->save($author_photo);

		$this->assertFalse($admin->can('delete', $author_photo->fresh()));

		$admin->group->author_edit = true;
		$admin->push();

		$this->assertTrue($admin->can('delete', $author_photo->fresh()));
	}

	public function testAuthorPrivatePolicy()
	{
		$admin = factory(User::class)->create();

		$author = factory(Author::class)
			->create();
		$author->statusPrivate();
		$author->save();

		$author_photo = new AuthorPhoto;
		$author_photo->openImage(__DIR__ . '/../images/test.jpeg');
		$author->photos()->save($author_photo);

		$this->assertFalse($admin->can('delete', $author_photo->fresh()));
		$this->assertTrue($author->create_user->can('delete', $author_photo->fresh()));
	}

	public function testIndexHttp()
	{
		$author = factory(Author::class)
			->create();

		$this->get(route('authors.photos.index', ['author' => $author]))
			->assertRedirect(route('authors.show', ['author' => $author]));
	}

	public function testShowHttp()
	{
		$author = factory(Author::class)
			->states('with_photo')
			->create();

		$this->actingAs($author->create_user)
			->get(route('authors.photo', ['author' => $author]))
			->assertOk()
			->assertViewHas('author', $author)
			->assertViewIs('author.photo.show');
	}

	public function testShowAuthorNotFound()
	{
		$author = factory(Author::class)
			->states('with_photo')
			->create();

		$author->delete();

		$this->actingAs($author->create_user)
			->get(route('authors.photo', ['author' => $author]))
			->assertNotFound();
	}

	public function testShowPhotoNotFound()
	{
		$author = factory(Author::class)
			->states('with_photo')
			->create();

		$author->photo->delete();

		$this->actingAs($author->create_user)
			->get(route('authors.photo', ['author' => $author]))
			->assertNotFound();
	}
}
