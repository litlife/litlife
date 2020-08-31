<?php

namespace Litlife\Epub\Tests;

class UnifyImageNamesTest extends TestCase
{
	private $prefix = '';

	public function testRename()
	{
		$this->newEpub();

		$this->addImage('image_2.jpg', 'Images/image_2.jpg', 'image/png');

		$this->addImage('image_2.jpg', 'Images2/image_2.jpg', 'image/png');

		$this->addSection('section1.xhtml', '<p><img src="../Images2/image_2.jpg" /></p>');

		$this->addSection('section2.xhtml', '<p><img src="../Images/image_2.jpg" /></p>');

		$this->assertEquals(2, $this->getEpub()->unifyImagesNames()->getMaxId());

		$this->getEpub()->unifyImagesNames()->unify();

		$this->assertEquals('<p><img src="../Images2/image_3.jpg"/></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyContent());

		$this->assertEquals('<p><img src="../Images/image_2.jpg"/></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyContent());

		$images = $this->getEpub()->getImages();

		$this->assertEquals('image_3.jpg',
			$images['OEBPS/Images2/image_3.jpg']->getBaseName());

		$this->assertEquals('image_2.jpg',
			$images['OEBPS/Images/image_2.jpg']->getBaseName());

		$this->assertEquals('Images/image_2.jpg', $this->getEpub()->opf()->getManifestItemById('image_2.jpg')->item(0)->getAttribute('href'));
		$this->assertEquals('Images2/image_3.jpg', $this->getEpub()->opf()->getManifestItemById('image_3.jpg')->item(0)->getAttribute('href'));
	}

	public function testRenameWithCaseInsensitive()
	{
		$this->newEpub();

		$this->addImage('image_2.jpg', 'Images/image_2.jpg', 'image/png');
		$this->addImage('image_2.JPEG', 'Images2/image_2.JPG', 'image/png');

		$this->addSection('section1.xhtml', '<p><img src="../Images2/image_2.JPG" /></p>');
		$this->addSection('section2.xhtml', '<p><img src="../Images/image_2.jpg" /></p>');

		$this->assertEquals(2, $this->getEpub()->unifyImagesNames()->getMaxId());
		$this->getEpub()->unifyImagesNames()->unify();

		$this->assertEquals('<p><img src="../Images2/image_3.JPG"/></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyContent());

		$this->assertEquals('<p><img src="../Images/image_2.jpg"/></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyContent());

		$images = $this->getEpub()->getImages();

		$this->assertEquals('image_3.JPG',
			$images['OEBPS/Images2/image_3.JPG']->getBaseName());

		$this->assertEquals('image_2.jpg',
			$images['OEBPS/Images/image_2.jpg']->getBaseName());

		$this->assertEquals('Images/image_2.jpg', $this->getEpub()->opf()->getManifestItemById('image_2.jpg')->item(0)->getAttribute('href'));
		$this->assertEquals('Images2/image_3.JPG', $this->getEpub()->opf()->getManifestItemById('image_3.JPG')->item(0)->getAttribute('href'));
	}
}
