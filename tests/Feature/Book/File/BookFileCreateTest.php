<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use App\User;
use Exception;
use Illuminate\Support\Facades\Storage;
use Litlife\Url\Url;
use Tests\TestCase;

class BookFileCreateTest extends TestCase
{
    public function testCreateHttp()
    {
        $user = User::factory()->create();
        $user->group->book_file_add = true;
        $user->push();

        $book = Book::factory()->create();

        $this->actingAs($user)
            ->get(route('books.files.create', ['book' => $book]))
            ->assertOk();
    }

    public function testCreate()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        //$file->zip = true;
        $file->open(__DIR__.'/../Books/test.fb2');
        $file->statusAccepted();
        $book->files()->save($file);

        $file->refresh();

        $this->assertEquals(1, $book->files->count());
        $this->assertEquals('fb2', $file->format);
        $this->assertEquals($file->file_size, $file->size);
        $this->assertNotNull($file->md5);
        $this->assertTrue($file->exists());
        $this->assertTrue(is_resource($file->getStream()));
        $this->assertEquals(Url::fromString($file->name)->getExtension(), $file->format);
    }

    public function testCreateInputZip()
    {
        config(['litlife.disk_for_files' => 'private']);

        $book = Book::factory()->create();

        $file = new BookFile;
        //$file->zip = true;
        $file->open(__DIR__.'/../Books/test_95.doc.zip');
        $file->statusAccepted();
        $book->files()->save($file);

        $file->refresh();

        $this->assertEquals(1, $book->files->count());
        $this->assertEquals('private', $file->storage);
        $this->assertEquals('doc', $file->format);
        $this->assertEquals($file->file_size, $file->size);
        $this->assertNotNull($file->md5);
        $this->assertTrue($file->exists());
        $this->assertTrue(is_resource($file->getStream()));
        $this->assertEquals(Url::fromString($file->name)->getExtension(), $file->format);
    }

    public function testCreateInputFile()
    {
        $string = uniqid();

        $tmp = tmpfile();
        fwrite($tmp, $string);
        $uri = stream_get_meta_data($tmp)['uri'];

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->open($uri, 'txt');
        $file->statusAccepted();
        $book->files()->save($file);

        $file->refresh();

        $this->assertEquals('txt', $file->format);
        $this->assertEquals($file->file_size, $file->size);
        $this->assertEquals(strlen($string), $file->size);
        $this->assertEquals(md5($string), $file->md5);
        $this->assertTrue($file->exists());
        $this->assertTrue(is_resource($file->getStream()));
        $this->assertEquals(Url::fromString($file->name)->getExtension(), $file->format);
        $this->assertEquals($string, $file->getContents());
        $this->assertEquals(strlen($string), $file->getSize());
    }

    public function testCreateOutputZip()
    {
        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/../Books/test.fb2');
        $file->statusAccepted();
        $book->files()->save($file);

        $file->refresh();

        $this->assertEquals(1, $book->files->count());

        $this->assertEquals('fb2', $file->format);
        $this->assertNotEquals($file->file_size, $file->size);
        $this->assertNotNull($file->md5);
        $this->assertTrue($file->exists());
        $this->assertTrue(is_resource($file->getStream()));
        $this->assertEquals('zip', Url::fromString($file->name)->getExtension());
        $this->assertEquals('fb2', Url::fromString(Url::fromString($file->name)->getFilename())->getExtension());

        $this->assertTrue($file->isZipArchive());

        $this->assertEquals(Url::fromString($file->name)->getFilename(), $file->getFirstFileInArchive());
    }

    public function testCreateInputAndOutputZip()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/../Books/test_95.doc.zip');
        $file->statusAccepted();
        $book->files()->save($file);

        $file->refresh();

        $this->assertEquals(1, $book->files->count());

        $this->assertEquals('doc', $file->format);
        $this->assertNotEquals($file->file_size, $file->size);
        $this->assertNotNull($file->md5);
        $this->assertTrue($file->exists());
        $this->assertTrue(is_resource($file->getStream()));
        $this->assertEquals('zip', Url::fromString($file->name)->getExtension());
        $this->assertEquals('doc', Url::fromString(Url::fromString($file->name)->getFilename())->getExtension());

        $this->assertTrue($file->isZipArchive());

        $this->assertEquals(Url::fromString($file->name)->getFilename(), $file->getFirstFileInArchive());
    }

    public function testNotFoundException()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;

        try {
            $file->open(__DIR__.'/../Books/'.uniqid());
            $this->assertFalse(true);

        } catch (Exception $exception) {
            $this->assertEquals($exception->getMessage(), 'File or resource not found');
        }
    }
}
