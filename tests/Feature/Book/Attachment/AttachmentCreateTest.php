<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use App\Book;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentCreateTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Storage::fake(config('filesystems.default'));
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreate()
	{
		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../../images/test.jpeg');
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
		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../../images/test.jpeg');
		$book->attachments()->save($attachment);

		$attachment->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);


		$attachment = new Attachment;
		$attachment->openImage(__DIR__ . '/../../images/test.jpeg');
		$book->attachments()->save($attachment);

		$attachment->refresh();

		$this->assertNotEquals('test.jpeg', $attachment->name);
		$this->assertEquals('image/jpeg', $attachment->content_type);
		$this->assertEquals('image', $attachment->type);

		$this->assertEquals(2, $book->attachments()->count());
	}
}
