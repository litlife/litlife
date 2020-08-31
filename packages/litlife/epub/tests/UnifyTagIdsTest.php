<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\UnifyTagIds;

class UnifyTagIdsTest extends TestCase
{
	private $prefix = '';

	public function testMaxId()
	{
		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p id="note-1">text</p>');
		$this->addSection('Section0002.xhtml', '<p id="note-1">text</p>');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$this->assertEquals(1, $unify->getMaxId());

		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p id="note-4">text</p>');
		$this->addSection('Section0002.xhtml', '<p id="note-4">text</p>');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$this->assertEquals(4, $unify->getMaxId());
	}

	public function testUnifyWithoutLinks()
	{
		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p id="note-1">text</p>');
		$this->addSection('Section0002.xhtml', '<p id="note-1">text</p>');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$unify->unify();

		$this->assertEquals('<p id="note-1">text</p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0001.xhtml')->getBodyContent());

		$this->assertEquals('<p id="note-2">text</p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0002.xhtml')->getBodyContent());
	}

	public function testUnifyWithoutLinksOnlySectionIds()
	{
		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p>text</p>', 'note-1');
		$this->addSection('Section0002.xhtml', '<p>text</p>', 'note-1');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$unify->unify();

		$this->assertEquals('note-1',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0001.xhtml')->getBodyId());

		$this->assertEquals('note-2',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0002.xhtml')->getBodyId());
	}

	public function testReplaceFragmentInAnchors()
	{
		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p>text <a id="note-1">link</a></p>');
		$this->addSection('Section0002.xhtml', '<p>text <a id="note-1">link</a></p>');
		$this->addSection('Section0003.xhtml', '<p><a href="../Text/Section0002.xhtml#note-1">link</a> text text</p>');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$unify->unify();

		$this->assertEquals('<p>text <a id="note-1">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0001.xhtml')->getBodyContent());

		$this->assertEquals('<p>text <a id="note-2">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0002.xhtml')->getBodyContent());

		$this->assertEquals('<p><a href="../Text/Section0002.xhtml#note-2">link</a> text text</p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0003.xhtml')->getBodyContent());
	}

	public function testReplaceFragmentInAnchors2()
	{
		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p><a id="note-1">link</a></p>');
		$this->addSection('Section0002.xhtml', '<p><a href="../Text/Section0002.xhtml#note-1">link</a> <a id="note-1">link</a></p>');
		$this->addSection('Section0003.xhtml', '<p><a href="../Text/Section0003.xhtml#note-1">link</a></p>');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$unify->unify();

		$this->assertEquals('<p><a id="note-1">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0001.xhtml')->getBodyContent());

		$this->assertEquals('<p><a href="../Text/Section0002.xhtml#note-2">link</a> <a id="note-2">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0002.xhtml')->getBodyContent());

		$this->assertEquals('<p><a href="../Text/Section0003.xhtml#note-2">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0003.xhtml')->getBodyContent());
	}

	public function testReplaceFragmentInAnchors4()
	{
		$this->newEpub();

		$this->addSection('Section0001.xhtml', '<p><a id="note-1">link</a></p>');
		$this->addSection('Section0002.xhtml', '<p><a href="../Text/Section0002.xhtml#note-1">link</a> <a id="note-1">link</a></p>');
		$this->addSection('Section0003.xhtml', '<p><a href="../Text/Section0002.xhtml#note-1">link</a> <a id="note-1">link</a></p>');

		$unify = new UnifyTagIds($this->getEpub(), $this->prefix);
		$unify->unify();

		$this->assertEquals('<p><a id="note-1">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0001.xhtml')->getBodyContent());

		$this->assertEquals('<p><a href="../Text/Section0002.xhtml#note-2">link</a> <a id="note-2">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0002.xhtml')->getBodyContent());

		$this->assertEquals('<p><a href="../Text/Section0002.xhtml#note-2">link</a> <a id="note-3">link</a></p>',
			$this->getEpub()->getSectionByFilePath('OEBPS/Text/Section0003.xhtml')->getBodyContent());
	}
}
