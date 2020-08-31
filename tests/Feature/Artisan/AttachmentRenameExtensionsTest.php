<?php

namespace Tests\Feature\Artisan;

use App\Attachment;
use App\Book;
use App\Section;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentRenameExtensionsTest extends TestCase
{
	public function testRenamed()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->refresh();
		$attachment->name = 'test';

		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.jpeg', $attachment->dirname . '/test');

		$attachment->save();
		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		Artisan::call('attachments:rename_extensions', ['book_id' => $book->id]);

		$section->refresh();

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/test.jpeg" alt="test.jpeg"/></p>', $section->getContent());
	}

	public function testNotRenamed()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.gif');
		$book->attachments()->save($attachment);
		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		Artisan::call('attachments:rename_extensions', ['book_id' => $book->id]);

		$section->refresh();

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/test.gif" alt="test.gif"/></p>', $section->getContent());
	}

	public function testRenameIfFileExists()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->refresh();
		$attachment->name = 'test';

		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.jpeg', $attachment->dirname . '/test');

		$attachment->save();
		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$attachment2 = new Attachment();
		$attachment2->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment2);

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		Artisan::call('attachments:rename_extensions', ['book_id' => $book->id]);

		$section->refresh();

		$attachment->refresh();

		$this->assertRegExp('/test_([[:alnum:]]{13})\.jpeg/iu', $attachment->name);

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/' . $attachment->name . '" alt="' . $attachment->name . '"/></p>',
			$section->getContent());
	}

	public function testSearchAndRename()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->refresh();
		$attachment->name = 'test';

		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.jpeg', $attachment->dirname . '/test');

		$attachment->save();
		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		Artisan::call('attachments:search_rename_extensions', ['last_book_id' => $book->id]);

		$section->refresh();

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/test.jpeg" alt="test.jpeg"/></p>', $section->getContent());
	}

	public function testRenameIfExtensionUpperCase()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->refresh();

		DB::table('attachments')
			->where('id', $attachment->id)
			->update(['name' => 'test.JPEG']);

		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.jpeg', $attachment->dirname . '/test.JPG');
		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.JPG', $attachment->dirname . '/test.JPEG');

		$attachment->save();
		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		Artisan::call('attachments:rename_extensions', ['book_id' => $book->id]);

		$section->refresh();
		$attachment->refresh();

		$this->assertEquals('test.jpeg', $attachment->name);

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/test.jpeg" alt="test.jpeg"/></p>', $section->getContent());
	}

	public function testUpdateOnlySectionsWithReplacedImageUrls()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->name = 'test';
		$attachment->save();

		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.jpeg', $attachment->dirname . '/test');

		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$section = new Section();
		$section->title = uniqid();
		$section->type = 'section';
		$section->content = '<p><img src="' . $attachment->url . '" /></p>';
		$book->sections()->save($section);
		$section->refresh();

		$section2 = new Section();
		$section2->title = uniqid();
		$section2->type = 'section';
		$section2->content = '<p>текст</p>';
		$book->sections()->save($section2);

		$page_id = $section2->pages()->first()->id;

		Artisan::call('attachments:rename_extensions', ['book_id' => $book->id]);

		$section->refresh();

		$this->assertEquals($page_id, $section2->pages()->first()->id);

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/test.jpeg" alt="test.jpeg"/></p>', $section->getContent());
	}

	public function testOtherImages()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')
			->create();

		$attachment = new Attachment();
		$attachment->openImage(__DIR__ . '/../images/test.jpeg');
		$book->attachments()->save($attachment);
		$attachment->refresh();
		$attachment->name = 'test0';

		Storage::disk(config('filesystems.default'))->rename($attachment->dirname . '/test.jpeg', $attachment->dirname . '/test0');

		$attachment->save();
		$attachment->refresh();

		$this->assertTrue($attachment->exists());

		$attachment2 = new Attachment();
		$attachment2->openImage(__DIR__ . '/../images/test.gif');
		$book->attachments()->save($attachment2);
		$attachment2->refresh();
		$attachment2->name = 'test2';

		Storage::disk(config('filesystems.default'))->rename($attachment2->dirname . '/test.gif', $attachment2->dirname . '/test2');

		$attachment2->save();
		$attachment2->refresh();

		$this->assertTrue($attachment->exists());

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

		$this->assertEquals('test0', $attachment->name);
		$this->assertEquals('test2', $attachment2->name);

		Artisan::call('attachments:search_rename_extensions', ['last_book_id' => $book->id]);

		$section->refresh();

		$attachment->refresh();
		$attachment2->refresh();

		$this->assertEquals('test0.jpeg', $attachment->name);
		$this->assertEquals('test2.gif', $attachment2->name);

		$this->assertEquals('<p><img src="/storage/' . $attachment->dirname . '/test0.jpeg" alt="test0.jpeg"/></p>', $section->getContent());
		$this->assertEquals('<p><img src="/storage/' . $attachment2->dirname . '/test2.gif" alt="test2.gif"/></p>', $section2->getContent());
	}
}
