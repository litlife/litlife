<?php

namespace Litlife\Epub\Tests;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Litlife\Epub\Epub;
use Litlife\Epub\Image;
use Litlife\Epub\Section;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase
{
	private $epub;

	public function newEpub()
	{
		$this->epub = new Epub();
		$this->epub->createContainer();
		$this->epub->createOpf();

		return $this->epub;
	}

	public function getEpub()
	{
		return $this->epub;
	}

	public function addSection($name, $text, $body_id = null)
	{
		$section = new Section($this->epub);
		$section->setPath($this->epub->default_folder . '/Text/' . $name);

		if (!empty($text))
			$section->setBodyHtml($text);

		if (!empty($body_id))
			$section->setBodyId($body_id);

		$this->epub->opf()->appendToManifest($name, 'Text/' . $name, 'application/xhtml+xml');
		$this->epub->opf()->appendToSpine($name);
	}

	public function addImage($name, $path, $mimeType, $content = null)
	{
		if (empty($content))
			$content = $this->mockImage()->getImageBlob();

		$image = new Image($this->epub);
		$image->setPath($this->epub->default_folder . '/' . $path);
		$image->setContent($content);
		$this->epub->opf()->appendToManifest($name, $path, $mimeType);
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

	public function nl($s)
	{
		return str_replace("\n", "\r\n", $s);
	}
}