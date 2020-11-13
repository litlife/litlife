<?php

namespace Tests\Feature\Artisan;

use App\BookFile;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookAppendFromStorageTest extends TestCase
{
    public function testAppend()
    {
        Storage::fake('public');

        $contents = Str::random(64);

        $user = User::factory()->create();

        Storage::disk('public')->put('/test/file.fb2', $contents);

        $this->artisan('book:append_from_storage', ['--disk' => 'public', '--directory' => '/test', '--create_user_id' => $user->id])
            //->expectsOutput('file.fb2')
            ->assertExitCode(0);

        $file = BookFile::where('md5', md5($contents))->first();

        $this->assertNotNull($file);
        $this->assertEquals('file.fb2', $file->name);
        $this->assertEquals(64, $file->size);
        $this->assertTrue($file->isSource());
        $this->assertEquals($user->id, $file->create_user_id);
        $this->assertEquals($contents, $file->getContents());

        $book = $file->book;

        $this->assertEquals($user->id, $book->create_user_id);
        $this->assertEquals('file.fb2', $book->title);
        $this->assertTrue($book->isPrivate());
        $this->assertTrue($book->parse->isWait());
    }

    public function testAppendMd5CheckDuplicate()
    {
        Storage::fake('public');

        $contents = Str::random(64);

        $user = User::factory()->create();

        Storage::disk('public')->put('/test/file.fb2', $contents);
        Storage::disk('public')->put('/test/file2.fb2', $contents);

        $this->artisan('book:append_from_storage', ['--disk' => 'public', '--directory' => '/test', '--create_user_id' => $user->id])
            //->expectsOutput('file.fb2')
            ->assertExitCode(0);

        $this->assertEquals(1, BookFile::where('md5', md5($contents))->count());

        $file = BookFile::where('md5', md5($contents))->first();

        $this->assertNotNull($file);
        $this->assertEquals('file.fb2', $file->name);
    }

    public function testRemoveAfterAppend()
    {
        Storage::fake('public');

        $contents = Str::random(64);

        $user = User::factory()->create();

        Storage::disk('public')->put('/test/file.fb2', $contents);

        $this->artisan('book:append_from_storage', [
            '--disk' => 'public',
            '--directory' => '/test',
            '--create_user_id' => $user->id,
            '--remove_after_add' => true
        ])
            //->expectsOutput('file.fb2')
            ->assertExitCode(0);

        $this->assertFalse(Storage::disk('public')->exists('/test/file.fb2'));
    }

    public function testDontRemoveAfterAppend()
    {
        Storage::fake('public');

        $contents = Str::random(64);

        $user = User::factory()->create();

        Storage::disk('public')->put('/test/file.fb2', $contents);

        $this->artisan('book:append_from_storage', [
            '--disk' => 'public',
            '--directory' => '/test',
            '--create_user_id' => $user->id,
            '--remove_after_add' => false
        ])
            //->expectsOutput('file.fb2')
            ->assertExitCode(0);

        $this->assertTrue(Storage::disk('public')->exists('/test/file.fb2'));
    }

    public function testAllowedExtensions()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Storage::disk('public')->put('/test/file.fb2', Str::random(64));
        Storage::disk('public')->put('/test/file.doc', Str::random(64));

        $this->artisan('book:append_from_storage', [
            '--disk' => 'public',
            '--extensions' => 'doc',
            '--directory' => '/test',
            '--create_user_id' => $user->id,
            '--remove_after_add' => false
        ])->assertExitCode(0);

        $this->assertFalse(BookFile::where('md5', md5(Storage::disk('public')->get('/test/file.fb2')))->exists());
        $this->assertTrue(BookFile::where('md5', md5(Storage::disk('public')->get('/test/file.doc')))->exists());
    }

    public function testTwoFiles()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Storage::disk('public')->put('/test/file.fb2', Str::random(64));
        Storage::disk('public')->put('/test/file2.fb2', Str::random(64));

        $this->artisan('book:append_from_storage', [
            '--disk' => 'public',
            '--extensions' => 'fb2',
            '--directory' => '/test',
            '--create_user_id' => $user->id,
            '--remove_after_add' => false
        ])->assertExitCode(0);

        $this->assertTrue(BookFile::where('md5', md5(Storage::disk('public')->get('/test/file.fb2')))->exists());
        $this->assertTrue(BookFile::where('md5', md5(Storage::disk('public')->get('/test/file2.fb2')))->exists());
    }
}
