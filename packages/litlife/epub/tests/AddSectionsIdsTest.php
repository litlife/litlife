<?php

namespace Litlife\Epub\Tests;

class AddSectionsIdsTest extends TestCase
{
	public function testIfNoExists()
	{
		$this->newEpub();

		$this->addSection('section1.xhtml', '<p>123</p>');
		$this->addSection('section2.xhtml', '<p>456</p>');
		$this->addSection('section3.xhtml', '<p>789</p>');

		$this->getEpub()->addSectionsIds()->init();

		$this->assertEquals('section-1', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyId());
		$this->assertEquals('section-2', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyId());
		$this->assertEquals('section-3', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section3.xhtml')->getBodyId());
	}

	public function testIfExists()
	{
		$this->newEpub();

		$this->addSection('section1.xhtml', '<p>123</p>');
		$this->addSection('section2.xhtml', '<p>456</p>', 'some_id');
		$this->addSection('section3.xhtml', '<p>789</p>');

		$this->getEpub()->addSectionsIds()->init();

		$this->assertEquals('section-1', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyId());
		$this->assertEquals('section-2', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyId());
		$this->assertEquals('section-3', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section3.xhtml')->getBodyId());
	}

	public function testWithAnchor()
	{
		$this->newEpub();

		$this->addSection('section1.xhtml', '<p>123</p>');
		$this->addSection('section2.xhtml', '<p><a href="../Text/section3.xhtml#some_id">link</a></p>');
		$this->addSection('section3.xhtml', '<p>789</p>', 'some_id');

		$this->getEpub()->addSectionsIds()->init();

		$this->assertEquals('section-1', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyId());
		$this->assertEquals('section-2', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyId());
		$this->assertEquals('section-3', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section3.xhtml')->getBodyId());

		$this->assertEquals('<p><a href="../Text/section3.xhtml#section-3">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyContent());
	}

	public function testWithEnexistedAnchor()
	{
		$this->newEpub();

		$this->addSection('section1.xhtml', '<p>123</p>');
		$this->addSection('section2.xhtml', '<p><a href="../Text/section3.xhtml#some_id">link</a></p>');
		$this->addSection('section3.xhtml', '<p>789</p>');

		$this->getEpub()->addSectionsIds()->init();

		$this->assertEquals('section-1', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section1.xhtml')->getBodyId());
		$this->assertEquals('section-2', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyId());
		$this->assertEquals('section-3', $this->getEpub()->getSectionByFilePath('OEBPS/Text/section3.xhtml')->getBodyId());

		$this->assertEquals('<p><a href="../Text/section3.xhtml#some_id">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/section2.xhtml')->getBodyContent());
	}
}
