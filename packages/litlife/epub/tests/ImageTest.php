<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Epub;
use Litlife\Epub\Image;

class ImageTest extends TestCase
{
	public function testRename()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$this->assertInstanceOf(Image::class, $epub->getImageByFilePath('OEBPS/Images/test.png'));

		$image = $epub->getImageByFilePath('OEBPS/Images/test.png');

		$image->rename('test2.png');

		$section = $epub->getSectionByFilePath('OEBPS/Text/Section0001.xhtml');
		$this->assertEquals('../Images/test2.png', $section->dom()->getElementsByTagName('img')->item(0)->getAttribute('src'));

		$this->assertNull($epub->getImageByFilePath('OEBPS/Images/test.png'));
		$this->assertInstanceOf(Image::class, $epub->getImageByFilePath('OEBPS/Images/test2.png'));

		$image = $epub->getImageByFilePath('OEBPS/Images/test2.png');
		$this->assertNotNull($image->getContent());
		$this->assertNotNull($image->getSize());
	}

	public function testSaveOpen()
	{
		$epub = $this->newEpub();

		$width = rand(200, 300);
		$height = rand(200, 300);

		$imagick = $this->mockImage($width, $height);

		$tmp = tmpfile();
		fwrite($tmp, $imagick->getImageBlob());
		fseek($tmp, 0);

		$content = stream_get_contents($tmp);

		$this->assertNotEmpty($content);
		$md5 = md5($content);
		$size = strlen($content);

		$this->addImage('test.jpeg', 'Images/test.jpeg', 'image/jpeg', $content);

		$string = $epub->outputAsString();

		$epub = new Epub();
		$epub->setFile($string);

		$image = $epub->getFileByPath($epub->default_folder . '/Images/test.jpeg');
		$this->assertInstanceOf(Image::class, $image);
		$this->assertEquals($size, strlen($image->getContent()));
		$this->assertEquals($md5, md5($image->getContent()));
		$this->assertEquals($width, $image->getWidth());
		$this->assertEquals($height, $image->getHeight());
	}

	public function testWidthHeightMimeType()
	{
		$epub = $this->newEpub();

		$width = rand(200, 300);
		$height = rand(200, 300);

		$imagick = $this->mockImage($width, $height);

		$this->addImage('test.jpeg', 'Images/test.jpeg', 'image/jpeg', $imagick->getImageBlob());

		$image = $epub->getFileByPath($epub->default_folder . '/Images/test.jpeg');

		$this->assertEquals($width, $image->getWidth());
		$this->assertEquals($height, $image->getHeight());
		$this->assertEquals('image/jpeg', $image->getContentType());
		$this->assertEquals('jpeg', $image->guessExtension());
	}

	public function testInvalidImage()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test_wrong_image.epub');

		$file = $epub->getFileByPath('OEBPS/Images/test.png');

		$this->assertInstanceOf(Image::class, $file);
		$this->assertFalse($file->isValid());
	}

	public function testValidImage()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$file = $epub->getFileByPath('OEBPS/Images/test.png');

		$this->assertInstanceOf(Image::class, $file);
		$this->assertTrue($file->isValid());
	}
}
