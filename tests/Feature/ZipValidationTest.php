<?php

namespace Tests\Feature;

use App\Rules\ZipContainsBookFileRule;
use App\Rules\ZipRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpZip\ZipFile;
use Tests\TestCase;

class ZipValidationTest extends TestCase
{


	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testValid()
	{
		Storage::fake(config('filesystems.default'));

		$request['zip'] = new UploadedFile(__DIR__ . '/Book/Books/test_95.doc.zip',
			'test_95.doc.zip',
			filesize(__DIR__ . '/Book/Books/test_95.doc.zip'), null, true);

		$validator = Validator::make($request, [
			'zip' => ['required', 'file', new ZipRule],
		]);

		$this->assertFalse(($validator->fails()));
	}

	public function testInvalid()
	{
		Storage::fake(config('filesystems.default'));

		$request['zip'] = new UploadedFile(__DIR__ . '/Book/Books/invalid.zip',
			'invalid.zip',
			filesize(__DIR__ . '/Book/Books/invalid.zip'), null, true);

		$validator = Validator::make($request, [
			'zip' => ['required', 'file', new ZipRule],
		]);

		$this->assertTrue(($validator->fails()));
		$this->assertEquals(__('validation.zip', ['attribute' => 'zip']), $validator->getMessageBag()->getMessages()['zip'][0]);
	}

	public function testContainsBookFile()
	{
		Storage::fake(config('filesystems.default'));

		$request['zip'] = new UploadedFile(__DIR__ . '/Book/Books/test_95.doc.zip',
			'test_95.doc.zip',
			filesize(__DIR__ . '/Book/Books/test_95.doc.zip'), null, true);

		$validator = Validator::make($request, [
			'zip' => ['required', 'file', new ZipContainsBookFileRule()],
		]);

		$this->assertFalse(($validator->fails()));
	}

	public function testNotContainsBookFile()
	{
		Storage::fake(config('filesystems.default'));

		$request['zip'] = new UploadedFile(__DIR__ . '/Book/Books/test.jpeg.zip',
			'test.jpeg.zip',
			filesize(__DIR__ . '/Book/Books/test.jpeg.zip'), null, true);

		$validator = Validator::make($request, [
			'zip' => ['required', 'file', new ZipContainsBookFileRule],
		]);

		$this->assertTrue(($validator->fails()));
		$this->assertEquals(__('validation.zip_book_file', ['attribute' => 'zip']), $validator->getMessageBag()->getMessages()['zip'][0]);
	}

	public function testEmptyExtension()
	{
		$path = stream_get_meta_data(tmpfile())['uri'];

		$zipFile = new ZipFile();
		$zipFile->addFromString('file_without_extension', 'content');
		$zipFile->saveAsFile($path);

		$zipFile = new ZipFile();
		$zipFile->openFile($path);

		$request['zip'] = new UploadedFile($path,
			'test.zip',
			filesize($path), null, true);

		$validator = Validator::make($request, [
			'zip' => ['required', 'file', new ZipContainsBookFileRule],
		]);

		$this->assertTrue(($validator->fails()));
		$this->assertEquals(__('validation.zip_book_file', ['attribute' => 'zip']), $validator->getMessageBag()->getMessages()['zip'][0]);
	}
}
