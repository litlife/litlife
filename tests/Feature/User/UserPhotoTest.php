<?php

namespace Tests\Feature\User;

use App\User;
use App\UserPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickPixel;
use Tests\TestCase;

class UserPhotoTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIndexHttp()
	{
		$user = User::factory()->create();

		$response = $this->actingAs($user)
			->get(route('users.photos.index', ['user' => $user]))
			->assertRedirect(route('profile', ['user' => $user]));
	}

	public function testEditHttp()
	{
		$user = User::factory()->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(route('users.edit', ['user' => $user]))
			->assertOk();
	}

	public function testUploadAvatar()
	{
		$user = User::factory()->create()
			->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$filename = uniqid();

		$jpeg_image_path = __DIR__ . '/../images/test.jpeg';

		$file = new UploadedFile($jpeg_image_path, $filename, null, null, true);

		$response = $this->actingAs($user)
			->followingRedirects()
			->post(route('users.photos.store', ['user' => $user]),
				['file' => $file]
			)->assertOk()
			->assertSeeText(__('user_photo.upload_success'));

		$photo = $user->photos()->first();
		$user->refresh();

		$this->assertTrue($photo->exists());
		$this->assertEquals($user->id, $photo->user_id);
		$this->assertEquals($photo->getRealWidth(), $photo->getWidth());
		$this->assertEquals($photo->getRealHeight(), $photo->getHeight());
		$this->assertEquals($photo->id, $user->avatar->id);
		$this->assertContains($photo->size, [98949, 100921]);
		$this->assertEquals('c0a04b088a4f4a8320e32a81ac5817925b2537501f2631e92f5ab1db1f52d419', $photo->imagick->getImageSignature());
		$this->assertEquals(1, $user->photos()->count());
		$this->assertEquals(1, $user->photos_count);
	}

	public function testUploadEmpty()
	{
		$user = User::factory()->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(route('users.edit', ['user' => $user]))
			->assertOk();

		$tmp = tmpfile();
		$file = new UploadedFile(stream_get_meta_data($tmp)['uri'], uniqid(), null, null, true);

		$response = $this->actingAs($user)
			->post(route('users.photos.store', ['user' => $user]),
				['file' => $file]
			)->assertRedirect(route('users.edit', ['user' => $user]));

		$response->assertSessionHasErrorsIn('photo', ['file' => __('validation.image', ['attribute' => __('user_photo.file')])])
			->assertSessionHasErrorsIn('photo', ['file' => __('validation.dimensions', ['attribute' => __('user_photo.file')])]);

		$response = $this->actingAs($user)
			->get(route('users.edit', ['user' => $user]))
			->assertOk();

		$response = $this->actingAs($user)
			->followingRedirects()
			->post(route('users.photos.store', ['user' => $user]),
				['file' => $file]
			)->assertOk();

		$response->assertSeeText(__('validation.image', ['attribute' => __('user_photo.file')]))
			->assertSeeText(__('validation.dimensions', ['attribute' => __('user_photo.file')]));
	}

	public function testDeleteHttp()
	{
		$user = User::factory()->with_avatar()->create();
		$user->group->edit_profile = true;
		$user->push();

		$this->assertEquals(1, $user->photos_count);

		$this->assertTrue($user->exists());

		$response = $this->actingAs($user)
			->get(route('users.edit', ['user' => $user]))
			->assertOk();

		$photo = $user->photos()->first();

		$response = $this->actingAs($user)
			->followingRedirects()
			->get(route('users.photos.delete', ['user' => $user, 'photo' => $photo->id]))
			->assertOk()
			->assertSeeText(__('user_photo.deleted'));

		$photo->refresh();
		$user->refresh();

		$this->assertSoftDeleted($photo);
		$this->assertEquals(0, $user->photos_count);
	}

	public function testOpenImage()
	{
		$user = User::factory()->create()->fresh();

		$jpeg_image_path = __DIR__ . '/../images/test.jpeg';

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($jpeg_image_path);

		$user->photos()->save($photo);

		$photo->refresh();

		$this->assertTrue($photo->exists());
		$this->assertEquals($user->id, $photo->user_id);
		$this->assertEquals($photo->getRealWidth(), $photo->getWidth());
		$this->assertEquals($photo->getRealHeight(), $photo->getHeight());
	}

	public function testMaxWidth()
	{
		config(['litlife.max_user_photo_width' => 200]);
		config(['litlife.max_user_photo_height' => 100]);

		$user = User::factory()->create()->fresh();

		$image = new Imagick();
		$image->newImage(600, 300, new ImagickPixel('red'));
		$image->setImageFormat('jpeg');

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($image);
		$user->photos()->save($photo);

		$photo->refresh();

		$this->assertTrue($photo->exists());
		$this->assertEquals(200, $photo->getRealWidth());
		$this->assertEquals(100, $photo->getRealHeight());
	}

	public function testMaxHeight()
	{
		config(['litlife.max_user_photo_width' => 200]);
		config(['litlife.max_user_photo_height' => 100]);

		$user = User::factory()->create()->fresh();

		$image = new Imagick();
		$image->newImage(300, 600, new ImagickPixel('red'));
		$image->setImageFormat('jpeg');

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($image);
		$user->photos()->save($photo);

		$photo->refresh();

		$this->assertTrue($photo->exists());
		$this->assertEquals(50, $photo->getRealWidth());
		$this->assertEquals(100, $photo->getRealHeight());
	}

	public function testMaxHeightMaxWidthIfImageSmaller()
	{
		config(['litlife.max_user_photo_width' => 1000]);
		config(['litlife.max_user_photo_height' => 1000]);

		$user = User::factory()->create()->fresh();

		$image = new Imagick();
		$image->newImage(300, 300, new ImagickPixel('red'));
		$image->setImageFormat('jpeg');

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($image);
		$user->photos()->save($photo);

		$photo->refresh();

		$this->assertTrue($photo->exists());
		$this->assertEquals(300, $photo->getRealWidth());
		$this->assertEquals(300, $photo->getRealHeight());
	}

	public function testCreatePhotoPolicyIfCantEditProfile()
	{
		$user = User::factory()->create();
		$user->group->edit_profile = false;
		$user->push();

		$this->assertFalse($user->can('create_photo', $user));
	}

	public function testCreatePhotoPolicyIfCanEditProfile()
	{
		$user = User::factory()->create();
		$user->group->edit_profile = true;
		$user->push();

		$this->assertTrue($user->can('create_photo', $user));
	}

	public function testCreatePhotoPolicyForOtherUser()
	{
		$user = User::factory()->create();
		$user->group->edit_profile = true;
		$user->group->edit_other_profile = false;
		$user->push();

		$other_user = User::factory()->create();

		$this->assertTrue($user->can('create_photo', $user));
		$this->assertFalse($user->can('create_photo', $other_user));

		$user->group->edit_other_profile = true;
		$user->push();

		$this->assertTrue($user->can('create_photo', $user));
		$this->assertTrue($user->can('create_photo', $other_user));
	}

	public function testRemovePhotoPolicyIfCantEditProfile()
	{
		$user = User::factory()->with_avatar()->create();
		$user->group->edit_profile = false;
		$user->push();

		$this->assertFalse($user->can('remove_photo', $user));
	}

	public function testRemovePhotoPolicyIfCanEditProfile()
	{
		$user = User::factory()->with_avatar()->create();
		$user->group->edit_profile = true;
		$user->push();

		$this->assertTrue($user->can('remove_photo', $user));
	}

	public function testRemovePhotoPolicyForOtherUser()
	{
		$user = User::factory()->with_avatar()->create();
		$user->group->edit_profile = true;
		$user->group->edit_other_profile = false;
		$user->push();

		$other_user = User::factory()->with_avatar()->create();

		$this->assertTrue($user->can('remove_photo', $user));
		$this->assertFalse($user->can('remove_photo', $other_user));

		$user->group->edit_other_profile = true;
		$user->push();

		$this->assertTrue($user->can('remove_photo', $user));
		$this->assertTrue($user->can('remove_photo', $other_user));
	}

	public function testRemovePhotoPolicyIfAvatarNotExists()
	{
		$user = User::factory()->create();
		$user->group->edit_profile = true;
		$user->group->edit_other_profile = true;
		$user->push();

		$this->assertFalse($user->can('remove_photo', $user));
	}

	public function testChangeMiniature()
	{
		Storage::fake(config('filesystems.default'));

		$user = User::factory()->create()
			->fresh();

		$file = UploadedFile::fake()->image('avatar.jpg', 500, 500);

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($file->getRealPath());
		$user->photos()->save($photo);
		$user->avatar_id = $photo->id;
		$user->save();

		$user->refresh();

		$this->assertTrue(is_numeric($user->avatar_id));
		$this->assertTrue($user->avatar->exists());

		$this->actingAs($user)
			->get(route('users.set_miniature',
				['user' => $user->id, 'width' => 100, 'height' => 100, 'x' => 100, 'y' => 100]))
			->assertSessionHasNoErrors()
			->assertOk();

		$user->refresh();

		$miniature = $user->miniature;

		$this->assertNotNull($miniature);
		$this->assertEquals(100, $miniature->getRealWidth());
		$this->assertEquals(100, $miniature->getRealHeight());
	}

	public function testConvertIfNotSupportedImageFormat()
	{
		$user = User::factory()->create();

		$image = new Imagick();
		$image->newImage(300, 300, new ImagickPixel('red'));
		$image->setImageFormat('bmp3');

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($image);
		$user->photos()->save($photo);

		$photo->refresh();

		$this->assertTrue($photo->fileExists());
		$this->assertTrue($photo->getImagick() instanceof Imagick);
		$this->assertEquals(300, $photo->getImagick()->getImageHeight());
		$this->assertEquals(300, $photo->getImagick()->getImageWidth());
		$this->assertEquals('jpeg', mb_strtolower($photo->getImagick()->getImageFormat()));
	}

	public function testShowHttp()
	{
		$user = User::factory()->with_avatar()->create();

		$this->actingAs($user)
			->get(route('users.avatar.show', ['user' => $user]))
			->assertOk()
			->assertViewHas('user', $user)
			->assertViewIs('user.avatar.show');
	}

	public function testShowIfUserNotFound()
	{
		$user = User::factory()->with_avatar()->create();

		$user->avatar->delete();

		$this->actingAs($user)
			->get(route('users.avatar.show', ['user' => $user]))
			->assertNotFound();
	}

	public function testShowIfPhotoNotFound()
	{
		$user = User::factory()->with_avatar()->create();

		$user->delete();

		$this->actingAs($user)
			->get(route('users.avatar.show', ['user' => $user]))
			->assertNotFound();
	}
}
