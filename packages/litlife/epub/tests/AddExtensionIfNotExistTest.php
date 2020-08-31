<?php

namespace Litlife\Epub\Tests;

class AddExtensionIfNotExistTest extends TestCase
{
	public function testInit()
	{
		$this->newEpub();

		$this->addImage('image_2', 'Images/image_2', 'image/jpeg');

		$this->addSection('section1.xhtml', '<p><img src="../Images/image_2" /></p>');

		$this->getEpub()->addExtensionIfNotExist()->addExtension();

		$this->assertEquals('<p><img src="../Images/image_2.jpeg"/></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyContent());

		$this->assertEquals('Images/image_2.jpeg',
			$this->getEpub()->opf()->getManifestItemById('image_2.jpeg')->item(0)->getAttribute('href'));
	}
}
