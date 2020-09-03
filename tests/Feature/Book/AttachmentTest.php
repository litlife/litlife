<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Book;
use App\Jobs\AttachmentRenameJob;
use App\Section;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */

	public function testCreate()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);

		$attachment->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);
	}

	public function testStoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)
			->states('administrator')->create();

		$book = factory(Book::class)
			->states('with_section')
			->create();

		$image_path = $this->fakeImageStream();

		$file = new UploadedFile($image_path, 'test.jpg', null, null, true);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('books.attachments.store', ['book' => $book]), [
				'file' => $file
			])
			->assertOk()
			->assertSeeText(__('attachment.uploaded'));

		$attachment = $book->attachments()->first();

		$book->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);
		$this->assertEquals(filesize($image_path), $attachment->size);
		$this->assertEquals($user->id, $attachment->create_user_id);
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertFalse($attachment->isCover());
		$this->assertEquals(1, $book->attachments_count);
		$this->assertEquals($user->id, $book->edit_user_id);
	}

	public function testStoreHttpSetCover()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create();

		$image_path = $this->fakeImageStream();
		$file = new UploadedFile($image_path, 'test.jpg', null, null, true);

		$response = $this->actingAs($user)
			->followingRedirects()
			->post(route('books.attachments.store', ['book' => $book]), [
				'file' => $file, 'setCover' => true
			])
			->assertOk()
			->assertSeeText(__('attachment.uploaded'));

		$attachment = $book->attachments()->first();

		$this->assertTrue($attachment->isCover());

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('cover_add', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testStoreHttpFromSceditor()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->states('with_section')->create();

		$image_path = $this->fakeImageStream();
		$file = new UploadedFile($image_path, 'test.jpg', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.attachments.store_from_sceditor', ['book' => $book]), [
				'upload' => $file
			])
			->assertOk();

		$attachment = $book->attachments()->first();

		$this->assertNotNull($attachment);

		$response
			->assertViewIs('attachment.store_ckeditor')
			->assertViewHas('url', $attachment->url);

		$book->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);
		$this->assertEquals(filesize($image_path), $attachment->size);
		$this->assertEquals($user->id, $attachment->create_user_id);
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertFalse($attachment->isCover());
		$this->assertEquals(1, $book->attachments_count);
		$this->assertEquals($user->id, $book->edit_user_id);
	}

	public function testStoreHttpFromSceditorValidatorFails()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->states('with_section')->create();

		$tmp = tmpfile();

		$file = new UploadedFile(stream_get_meta_data($tmp)['uri'], 'test.jpg', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.attachments.store_from_sceditor', ['book' => $book]), [
				'upload' => $file
			])
			->assertOk();

		$this->assertEquals(0, $book->attachments()->count());

		$response
			->assertViewIs('attachment.store_ckeditor')
			->assertViewHas('message', __('validation.image', ['attribute' => 'upload']))
			->assertSeeText(__('validation.image', ['attribute' => 'upload']));
	}

	public function testStoreHttpFromSceditorResponseJson()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->states('with_section')->create();

		$image_path = $this->fakeImageStream();

		$file = new UploadedFile($image_path, 'test.jpg', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.attachments.store_from_sceditor', ['book' => $book]), [
				'upload' => $file, 'responseType' => 'json'
			])
			->assertOk();

		$attachment = $book->attachments()->first();

		$this->assertNotNull($attachment);

		$response->assertJson([
			'uploaded' => 1,
			'fileName' => $attachment->name,
			'url' => $attachment->url
		]);

		$book->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);
		$this->assertEquals(filesize($image_path), $attachment->size);
		$this->assertEquals($user->id, $attachment->create_user_id);
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertFalse($attachment->isCover());
		$this->assertEquals(1, $book->attachments_count);
		$this->assertEquals($user->id, $book->edit_user_id);
	}

	public function testStoreHttpFromSceditorResponseJsonFails()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->states('with_section')->create();

		$tmp = tmpfile();

		$file = new UploadedFile(stream_get_meta_data($tmp)['uri'], 'test.jpg', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.attachments.store_from_sceditor', ['book' => $book]), [
				'upload' => $file, 'responseType' => 'json'
			])
			->assertOk();

		$this->assertEquals(0, $book->attachments()->count());

		$response->assertJson([
			'uploaded' => 0,
			'fileName' => 'test.jpg',
			'error' => [
				'message' => __('validation.image', ['attribute' => 'upload'])
			]
		]);
	}

	public function testStoreHttpFromSceditorResponseJsonFailsFilenameIsEmpty()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->states('with_section')->create();

		$response = $this->actingAs($user)
			->post(route('books.attachments.store_from_sceditor', ['book' => $book]), [
				'upload' => '', 'responseType' => 'json'
			])
			->assertOk();

		$this->assertEquals(0, $book->attachments()->count());

		$response->assertJson([
			'uploaded' => 0,
			'fileName' => '',
			'error' => [
				'message' => __('validation.required', ['attribute' => 'upload'])
			]
		]);
	}

	public function testCreateWithSameName()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);

		$attachment->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);


		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);

		$attachment->refresh();

		$this->assertNotEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);

		$this->assertEquals(2, $book->attachments()->count());
	}

	public function testIsCover()
	{
		$cover = factory(Attachment::class)
			->states('cover')
			->create();

		$book = $cover->book;

		$attachment = factory(Attachment::class)
			->create(['book_id' => $book->id]);

		$this->assertTrue($cover->isCover());
		$this->assertFalse($attachment->isCover());
	}

	public function testSetAsCoverHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$attachment = factory(Attachment::class)->create();

		$book = $attachment->book;
		$book->sections_count = 10;
		$book->save();

		$this->assertFalse($attachment->isCover());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.attachments.set_cover', ['book' => $book, 'id' => $attachment->id]))
			->assertOk()
			->assertSeeText(__('attachment.selected_as_cover', ['name' => $attachment->name]));

		$attachment->refresh();
		$book->refresh();

		$this->assertTrue($attachment->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('set_cover', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testDetachCover()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$attachment = factory(Attachment::class)->states('cover')->create();

		$book = $attachment->book;
		$book->sections_count = 10;
		$book->save();

		$this->assertTrue($attachment->isCover());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.remove_cover', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('attachment.cover_removed'));

		$attachment->refresh();
		$book->refresh();

		$this->assertFalse($attachment->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);
		/*
				$this->assertEquals(1, $book->activities()->count());
				$activity = $book->activities()->first();
				$this->assertEquals('set_cover', $activity->description);
				$this->assertEquals($user->id, $activity->causer_id);
				$this->assertEquals('user', $activity->causer_type);
		*/
	}

	public function testDeleteHttp()
	{
		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)
			->states('with_cover', 'with_section')
			->create();

		$cover = $book->cover;

		$this->assertTrue($cover->isCover());

		$response = $this->actingAs($user)
			->delete(route('books.attachments.delete', ['book' => $book, 'id' => $cover]), [],
				['HTTP_X-Requested-With' => 'XMLHttpRequest']);

		$book->refresh();
		$cover->refresh();

		$response->assertOk()
			->assertJson($cover->toArray());

		$this->assertSoftDeleted($cover);
		$this->assertTrue($cover->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);
	}

	public function testRestoreHttp()
	{
		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)
			->states('with_cover', 'with_section')
			->create();

		$cover = $book->cover;

		$this->assertTrue($cover->isCover());

		$cover->delete();

		$response = $this->actingAs($user)
			->delete(route('books.attachments.delete', ['book' => $book, 'id' => $cover]), [],
				['HTTP_X-Requested-With' => 'XMLHttpRequest']);

		$book->refresh();
		$cover->refresh();

		$response->assertOk()
			->assertJson($cover->toArray());

		$this->assertFalse($cover->trashed());
		$this->assertTrue($cover->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);
	}

	public function testParameters()
	{
		$key = 'ключ';
		$value = 'значение';

		$attachment = new Attachment();
		$attachment->addParameter($key, $value);

		$this->assertEquals($value, $attachment->getParameter($key));

		$key = uniqid();

		$this->assertNull($attachment->getParameter($key));
	}

	public function testNameExtensionToLower()
	{
		$attachment = new Attachment();
		$attachment->name = 'имя.JPEG';

		$this->assertEquals('ima.jpeg', $attachment->name);
	}

	public function testSetAsCoverPolicy()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);

		$this->assertFalse($attachment->isCover());

		$this->assertTrue($user->can('setAsCover', $attachment));

		$book->cover()->associate($attachment);
		$book->save();
		$attachment->refresh();

		$this->assertTrue($attachment->isCover());

		$this->assertFalse($user->can('setAsCover', $attachment));
	}

	public function testFixAttachmentExtensionIfWrong()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpg_0');
		$book->attachments()->save($attachment);
		$attachment->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
	}

	public function testRename()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->refresh();

		$attachment2 = new Attachment();
		$attachment2->openImage(__DIR__ . '/../images/test.gif');
		$book->attachments()->save($attachment2);
		$attachment2->refresh();

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		$section2 = new Section();
		$section2->title = uniqid();
		$section2->type = 'section';
		$section2->content = '<p><img src="' . $attachment2->url . '" /></p>';
		$book->sections()->save($section2);
		$section2->refresh();

		$note = new Section();
		$note->title = uniqid();
		$note->type = 'note';
		$note->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($note);
		$section->refresh();

		$this->assertTrue($attachment->exists());
		$this->assertFalse($attachment->isZipArchive());

		AttachmentRenameJob::dispatch($book, $attachment, 'new_name.jpeg');

		$attachment->refresh();
		$section->refresh();
		$section2->refresh();
		$note->refresh();

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/new_name.jpeg" alt="new_name.jpeg"/></p>',
			$section->getContent());

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/new_name.jpeg" alt="new_name.jpeg"/></p>',
			$note->getContent());

		$this->assertEquals('<p><img src="/storage/' . $attachment2->dirname . '/test.gif" alt="test.gif"/></p>',
			$section2->getContent());
	}
}
