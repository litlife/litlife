<?php

namespace Tests\Feature\Book\File;

use App\BookFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFileTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Storage::fake(config('filesystems.default'));
	}

	public function testFactoryTxt()
	{
		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertTrue($file->exists());
		$this->assertEquals('txt', $file->format);
	}

	public function testGenerateDirname()
	{
		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertEquals(getPath($file->book->id) . '/' . $file->folder, $file->generateDirName());
	}

	public function testIsFileShouldBeArchived()
	{
		config(['litlife.not_zip_extensions' => ['zip', 'epub', 'djvu', 'mp3', 'ogg', 'odt', 'docx']]);

		$file = new BookFile();
		$file->zip = true;
		$file->format = 'ogg';

		$this->assertFalse($file->isShouldBeArchived());

		$file->format = 'fb2';

		$this->assertTrue($file->isShouldBeArchived());

		$file->zip = false;

		$this->assertFalse($file->isShouldBeArchived());

		$file->format = 'mp3';

		$this->assertFalse($file->isShouldBeArchived());
	}

	public function testDiskForFiles()
	{
		$disk = 'private';

		config(['litlife.disk_for_files' => $disk]);

		Storage::fake($disk);

		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertEquals($disk, $file->storage);

		$disk = 'public';

		config(['litlife.disk_for_files' => $disk]);

		Storage::fake($disk);

		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertEquals($disk, $file->storage);

		$file = factory(BookFile::class)
			->states('txt')
			->create(['storage' => 'private']);

		$this->assertEquals('private', $file->storage);
	}

	public function testCanParsed()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt', 'docx']]);
		config(['litlife.no_need_convert' => ['mp3', 'djvu']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$this->assertEquals('odt', $file->format);
		$this->assertTrue($file->canParsed());

		config(['litlife.book_allowed_file_extensions' => ['odt', 'docx']]);
		config(['litlife.no_need_convert' => ['mp3', 'djvu', 'odt']]);

		$this->assertFalse($file->canParsed());

		config(['litlife.book_allowed_file_extensions' => ['docx']]);
		config(['litlife.no_need_convert' => ['mp3', 'djvu']]);

		$this->assertFalse($file->canParsed());
	}
}