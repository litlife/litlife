<?php

namespace Litlife\Fb2\Tests;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Litlife\Fb2\Binary;
use Litlife\Fb2\Fb2;
use PHPUnit\Framework\TestCase;

class Fb2BinaryTest extends TestCase
{
	public function testGetBinariesArray()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');
		$fb2->loadBinaries();

		$binary = $fb2->getBinariesArray()['image.png'];

		$this->assertNotNull($binary);
		$this->assertEquals('image.png', $binary->getId());
		$this->assertEquals('image/png', $binary->getContentType());
	}

	public function testGetId()
	{
		$blob = $this->mockImage();

		$fb2 = new Fb2();
		$binary = new Binary($fb2, urlencode('имя.jpeg'));
		$binary->open($blob);

		$content = $fb2->getContent();

		$fb2 = new Fb2();
		$fb2->loadXML($content);
		$binary = pos($fb2->getBinariesArray());

		$this->assertEquals('имя.jpeg', $binary->getId());
	}

	public function mockImage($width = 320, $height = 240)
	{
		$image = new Imagick();
		$image->newImage($width, $height, new ImagickPixel('green'));
		$image->setImageFormat('jpeg');

		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel('gray'));
		$draw->setFontSize(12);
		$image->annotateImage($draw, 10, 45, 0, 'The quick brown fox jumps over the lazy dog');

		return $image;
	}

	public function testGetImageSignatureByName()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$binary = $fb2->getBinaryByName('image.png');

		$this->assertEquals('590f3519f7629dbb2bea72d81f3f2550adb22a56620cdd9c91fcdfbba5c8c358',
			$binary->getImagick()->getImageSignature());
	}
}
