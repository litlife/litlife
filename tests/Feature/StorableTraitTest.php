<?php

namespace Tests\Feature;

use App\BookFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use PhpZip\Exception\ZipException;
use Tests\TestCase;

class StorableTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();
    }

    public function testFactory()
    {
        $file = BookFile::factory()->txt()->create();

        $this->assertTrue($file->exists());
    }

    public function testFactoryZip()
    {
        $file = BookFile::factory()->txt()->zip()->create();

        $this->assertTrue($file->exists());
        $this->assertTrue($file->isZipArchive());
        $this->assertEquals('txt', $file->format);
    }

    public function testRename()
    {
        $file = BookFile::factory()->txt()->create();

        $new_name = 'new_file_name.txt';

        $file->rename($new_name);
        $file->refresh();

        $this->assertTrue($file->exists());
        $this->assertFalse($file->isZipArchive());
        $this->assertEquals('new_file_name.txt', $file->name);
    }

    public function testRenameZip()
    {
        $file = BookFile::factory()->txt()->zip()->create();

        $new_name = 'new_file_name.txt.zip';

        $file->rename($new_name);
        $file->refresh();

        $this->assertTrue($file->exists());
        $this->assertTrue($file->isZipArchive());
        $this->assertEquals('new_file_name.txt.zip', $file->name);
        $this->assertEquals('new_file_name.txt', $file->getFirstFileInArchive());
    }

    public function testRenameIfFileNotExists()
    {
        $file = BookFile::factory()->txt()->create();

        Storage::disk($file->storage)
            ->delete($file->dirname.'/'.$file->name);

        $this->assertFalse($file->exists());

        $old_name = $file->name;
        $new_name = 'new_file_name.txt';

        $this->expectException(FileNotFoundException::class);

        $file->rename($new_name);

        $file->refresh();

        $this->assertFalse($file->exists());
        $this->assertEquals($old_name, $file->name);
    }

    public function testRenameFileNotFoundInsideArchive()
    {
        $file = BookFile::factory()->txt()->zip()->create();

        $this->assertTrue($file->isZipArchive());

        $name = $file->getFirstFileInArchive();

        $file->getZipFile()->deleteFromName($name);

        Storage::disk($file->storage)
            ->put($file->dirname.'/'.$file->name, $file->getZipFile()->outputAsString());

        $this->assertEmpty($file->getZipFile()->getListFiles());

        $old_name = $file->name;
        $new_name = 'new_file_name.txt';

        $this->expectException(ZipException::class);
        $this->expectExceptionMessage('Not a single file was found in the archive');

        $file->rename($new_name);

        $file->refresh();

        $this->assertEquals($old_name, $file->name);
    }

    public function testName()
    {
        $file = BookFile::factory()->txt()->create();

        $file->name = 'Название файла';

        $this->assertEquals('Nazvanie_fajla', $file->name);

        $file->name = 'test   '.chr(32).'      test   '.chr(9).' '.chr(13).' '.chr(15).' test';

        $this->assertEquals('test_test_test', $file->name);

        $file->name = 'testʹы';

        $this->assertEquals('testy', $file->name);

        $file->name = 'test :*?"<>|+.,%!@ test / \ /';

        $this->assertEquals('test_._test', $file->name);

        $file->name = 'test ~`!@#$%^&*()[],.:;"\'"№;{} test';

        $this->assertEquals('test_~$^&()[].no_test', $file->name);

        $file->name = 'long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. long name. ';

        $this->assertEquals('long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name._long_name',
            $file->name);

        $file->name = 'test '.chr(194).' test '.chr(194).''.chr(194).''.chr(194).' test';

        $this->assertEquals('test_test_test', $file->name);

        $file->name = ' ?   書名   ';

        $this->assertEquals('shu_ming', $file->name);

        $file->name = 'título del libro  ';

        $this->assertEquals('titulo_del_libro', $file->name);

        $file->name = 'عنوان الكتاب';

        $this->assertEquals('ʿnwan_alktab', $file->name);

        $file->name = 'file.txt.zip';

        $this->assertEquals('file.txt.zip', $file->name);

        $file->name = 'file.fb2.zip';

        $this->assertEquals('file.fb2.zip', $file->name);

        $file->name = 'test___test.txt';

        $this->assertEquals('test_test.txt', $file->name);

        $file->name = 'test_ _ _test.txt';

        $this->assertEquals('test_test.txt', $file->name);

        $s = 'test';

        for ($a = 0; $a < 256; $a++) {
            $s .= chr($a);
        }

        $file->name = $s;

        $this->assertEquals('test_$&()_.0123456789abcdefghijklmnopqrstuvwxyz[]^_abcdefghijklmnopqrstuvwxyz~', $file->name);
    }

    public function testURLEncode()
    {
        $file = BookFile::factory()->txt()->create();

        $file->name = urlencode('файл_~`$^&()[].\'').' test.txt';

        $this->assertEquals('fajl_~$^&()[]._test.txt', $file->name);

        $file->name = urlencode(urlencode('файл'));

        $this->assertEquals('fajl', $file->name);

        $file->name = urlencode(urlencode(urlencode('файл')));

        $this->assertEquals('fajl', $file->name);

        $file->name = urlencode(urlencode(urlencode(urlencode('файл'))));

        $this->assertEquals('fajl', $file->name);

        $string = 'файл';

        for ($a = 0; $a < 51; $a++) {
            $string = urlencode($string);
        }

        $file->name = $string;

        $this->assertEquals('D184D0B0D0B9D0BB', $file->name);
    }

    public function testAvoidQuote()
    {
        $file = BookFile::factory()->txt()->create();

        $file->name = "fi''le.tx''t";

        $this->assertEquals('file.txt', $file->name);

        $file->name = "``fi``le.tx``t";

        $this->assertEquals('file.txt', $file->name);
    }

    public function testOverflow()
    {
        $file = new BookFile();

        $number = 200;

        $string = implode('', array_fill(0, $number + 1, 'a'));

        $file->name = $string.'.fb2.zip';

        $this->assertEquals(mb_substr($string, 0, $number - 8).'.fb2.zip', $file->name);

        $file->name = $string.'.zip';

        $this->assertEquals(mb_substr($string, 0, $number - 5).'.zip', $file->name);

        $file->name = $string.'.txt.fb2.zip';

        $this->assertEquals(mb_substr($string, 0, $number - 8).'.fb2.zip', $file->name);

        $file->name = $string.'.fb2fb2.zipip';

        $this->assertEquals(mb_substr($string, 0, $number - 7).'.zipip', $file->name);

        $file->name = $string.'.fb.zi';

        $this->assertEquals(mb_substr($string, 0, $number - 6).'.fb.zi', $file->name);

        $file->name = $string.'.f.z';

        $this->assertEquals(mb_substr($string, 0, $number), $file->name);
    }

    public function testMoveToAnotherStorage()
    {
        $old_filesystem = config('filesystems.default');
        $new_filesystem = 'local';

        Storage::fake($old_filesystem);
        Storage::fake($new_filesystem);

        $file = BookFile::factory()->txt()->create(['storage' => $old_filesystem]);

        $size = $file->size;
        $this->assertTrue(Storage::disk($old_filesystem)->exists($file->dirname.'/'.$file->name));
        $this->assertFalse(Storage::disk($new_filesystem)->exists($file->dirname.'/'.$file->name));
        $this->assertEquals($size, Storage::disk($old_filesystem)->size($file->dirname.'/'.$file->name));

        $file->moveToStorage($new_filesystem);
        $file->refresh();

        $this->assertTrue($file->exists());
        $this->assertEquals($size, Storage::disk($new_filesystem)->size($file->dirname.'/'.$file->name));
        $this->assertFalse(Storage::disk($old_filesystem)->exists($file->dirname.'/'.$file->name));
        $this->assertTrue(Storage::disk($new_filesystem)->exists($file->dirname.'/'.$file->name));
    }

    public function testIsStorageDeleteFileWorks()
    {
        Storage::fake('local');

        $file = BookFile::factory()->txt()->create(['storage' => 'local']);

        $this->assertTrue(Storage::disk('local')->exists($file->dirname.'/'.$file->name));

        $status = Storage::disk('local')
            ->delete($file->dirname.'/'.$file->name);

        $this->assertTrue($status);

        $this->assertFalse(Storage::disk('local')->exists($file->dirname.'/'.$file->name));
    }

    public function testFullUrlWithScheme()
    {
        Storage::fake('public');

        $file = BookFile::factory()->storage_public()->txt()->create();

        Storage::shouldReceive('disk')->times(3)->andReturn(new class()
        {
            public function url()
            {
                return '//example.com/file.txt';
            }
        });

        $this->assertEquals('//example.com/file.txt', $file->url);
        $this->assertEquals('https://example.com/file.txt', $file->getFullUrlWithScheme('https'));
        $this->assertEquals('http://example.com/file.txt', $file->getFullUrlWithScheme('http'));
    }

    public function testFilesystemDeleteDirectory()
    {
        Storage::fake('public');

        $dirName = uniqid();

        $fileName = $dirName.'/'.uniqid().'.txt';

        $storage = Storage::disk('public');

        $storage->makeDirectory($dirName);
        $storage->put($fileName, 'contents');

        $this->assertTrue($storage->exists($dirName));
        $this->assertTrue($storage->exists($fileName));

        $storage->deleteDirectory($dirName, true);

        $this->assertFalse($storage->exists($dirName));
        $this->assertFalse($storage->exists($fileName));
    }

    public function testPutNotAppend()
    {
        $file = uniqid();

        $storage = Storage::disk('public');

        $storage->put($file, 'text');

        $this->assertEquals('text', $storage->get($file));

        $storage->put($file, 'text');

        $this->assertEquals('text', $storage->get($file));
    }
}