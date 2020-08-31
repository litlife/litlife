<?php

namespace Litlife\Epub\Tests;

use DOMNode;
use Litlife\Epub\Container;
use Litlife\Epub\Epub;
use Litlife\Epub\Ncx;
use Litlife\Epub\Opf;
use Litlife\Epub\Section;

class NcxTest extends TestCase
{
	public function testGetNcxFullPath()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$this->assertEquals('OEBPS/toc.ncx', $epub->getNcxFullPath());
	}

	public function testGetNcxFullPathIfOpfDoesntExist()
	{
		$epub = new Epub();

		$this->assertFalse($epub->getNcxFullPath());
	}

	public function testGetNcxFullPathIfNotExists()
	{
		$epub = new Epub();

		$opf = new Opf($epub);
		$opf->setPath($epub->default_opf_path);

		$this->assertFalse($epub->getNcxFullPath());
	}

	public function testGetHead()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$head = $epub->ncx()->head();

		$this->assertNotNull($head);
		$this->assertInstanceOf(DOMNode::class, $head);
	}

	public function testGetNavmap()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$navmap = $epub->ncx()->navmap();

		$this->assertNotNull($navmap);
		$this->assertInstanceOf(DOMNode::class, $navmap);
	}

	public function testNewNcx()
	{
		$epub = new Epub();

		$ncx = new Ncx($epub);

		$s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
  <head/>
  <docTitle>
    <text/>
  </docTitle>
  <navMap/>
</ncx>

EOT;

		$this->assertEquals($s, $ncx->dom()->saveXML());
	}

	public function testGetFileById()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$navmap = $epub->ncx()->navmap();
		$navPoints = $epub->ncx()->getNavPoints($navmap);
		$navPoint = $navPoints->item(0);

		$this->assertEquals(1, $navPoints->count());

		$this->assertEquals('navPoint-1', $navPoint->getAttribute('id'));
		$this->assertEquals('1', $navPoint->getAttribute('playOrder'));

		$this->assertEquals('OEBPS/Text/Section0001.xhtml', $epub->ncx()->getFileById('navPoint-1')->getPath());
		$this->assertEquals('OEBPS/Text/Section0002.xhtml', $epub->ncx()->getFileById('navPoint-2')->getPath());
	}

	public function testGetTextById()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$this->assertEquals('Первая глава', $epub->ncx()->getTextById('navPoint-1'));
		$this->assertEquals('Вторая глава', $epub->ncx()->getTextById('navPoint-2'));
	}

	public function testAppendNavMap()
	{
		$epub = new Epub();

		$ncx = new Ncx($epub);
		$ncx->setPath($epub->default_ncx_path);

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section0001.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section0002.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');

		$parent = $ncx->appendNavMap('test', 'Text/Section0001.xhtml', '123', 3);

		$this->assertEquals('test', $epub->ncx()->getTextById('123'));
		$this->assertEquals('OEBPS/Text/Section0001.xhtml', $epub->ncx()->getFileById('123')->getPath());

		$ncx->appendNavMap('test2', 'Text/Section0002.xhtml', '124', 4, $parent);

		$this->assertEquals('test2', $epub->ncx()->getTextById('124'));
		$this->assertEquals('OEBPS/Text/Section0002.xhtml', $epub->ncx()->getFileById('124')->getPath());

		$navPoint = $ncx->getNavPoints($epub->ncx()->navmap())->item(0);

		$this->assertEquals('123', $navPoint->getAttribute('id'));

		$navPoint2 = $ncx->getNavPoints($navPoint)->item(0);

		$this->assertEquals('124', $navPoint2->getAttribute('id'));
	}

	public function testFindTitleByFullPath()
	{
		$epub = new Epub();

		$ncx = new Ncx($epub);
		$ncx->setPath($epub->default_ncx_path);

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section0001.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section0002.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');

		$parent = $ncx->appendNavMap('test', 'Text/Section0001.xhtml', '123', 3);
		$ncx->appendNavMap('test2', 'Text/Section0002.xhtml', '124', 4, $parent);

		$this->assertEquals('test', $ncx->findTitleByFullPath('OEBPS/Text/Section0001.xhtml'));
		$this->assertEquals('test2', $ncx->findTitleByFullPath('OEBPS/Text/Section0002.xhtml'));
	}

	public function testSaveOpenNcxExist()
	{
		$epub = $this->newEpub();

		$epub->createNcx();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section0001.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');

		$epub->ncx()->appendNavMap('test & — test', 'Text/Section0001.xhtml', '123', 3);

		$this->assertInstanceOf(Ncx::class, $epub->getFileByPath('OEBPS/toc.ncx'));

		$string = $epub->outputAsString();

		$epub = new Epub();
		$epub->setFile($string);

		$this->assertTrue($epub->zipFile->hasEntry('OEBPS/toc.ncx'));

		$this->assertContains('OEBPS/toc.ncx', $epub->getAllFilesList());
	}

	public function testCreateNew()
	{
		$epub = $this->newEpub();
		$epub->createContainer();
		$epub->createNcx();

		$this->assertInstanceOf(Container::class, $epub->container);
		$this->assertInstanceOf(Ncx::class, $epub->ncx);
	}

	public function testFileInAnotherFolder()
	{
		$epub = $this->newEpub();
		$epub->createContainer();
		$epub->createOpf();
		$epub->createNcx('OEBPS/ncx/toc.ncx');

		$this->assertEquals('<item id="ncx" href="ncx/toc.ncx" media-type="application/x-dtbncx+xml"/>',
			$epub->opf()->dom()->saveXml($epub->opf()->getManifestItemById('ncx')->item(0)));

		$epub = $this->newEpub();
		$epub->createContainer();
		$epub->createOpf();
		$epub->createNcx('/toc.ncx');

		$this->assertEquals('<item id="ncx" href="../toc.ncx" media-type="application/x-dtbncx+xml"/>',
			$epub->opf()->dom()->saveXml($epub->opf()->getManifestItemById('ncx')->item(0)));
	}

	public function testEntity()
	{
		$epub = $this->newEpub();
		$epub->createContainer();
		$epub->createOpf();
		$epub->createNcx('OEBPS/ncx/toc.ncx');

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section0001.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');

		$epub->ncx()->appendNavMap('Текст & — ', 'Text/&Section0001.xhtml', '123', 3);

		$xml = <<<EOL
<navPoint id="123">
  <navLabel>
    <text>Текст &amp; — </text>
  </navLabel>
  <content src="Text/&amp;Section0001.xhtml"/>
</navPoint>
EOL;

		$this->assertEquals($xml, $epub->ncx()->dom()->saveXml($epub->ncx()->navmap()->getElementsByTagName('navPoint')->item(0)));
	}

	public function testGetTree()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test_ncx_tree.epub');

		$tree = $epub->ncx()->getTree();

		$this->assertEquals(2, count($tree));
		$this->assertEquals(1, count($tree[0]['childs']));

		$string = serialize($tree);
		$this->assertEquals('a:2:{i:0;a:5:{s:2:"id";s:10:"navPoint-1";s:4:"text";s:12:"Глава 1";s:3:"src";s:22:"Text/Section0001.xhtml";s:9:"playOrder";s:0:"";s:6:"childs";a:1:{i:0;a:5:{s:2:"id";s:10:"navPoint-2";s:4:"text";s:18:"Подглава 2";s:3:"src";s:22:"Text/Section0002.xhtml";s:9:"playOrder";s:0:"";s:6:"childs";a:0:{}}}}i:1;a:5:{s:2:"id";s:10:"navPoint-3";s:4:"text";s:12:"Глава 3";s:3:"src";s:22:"Text/Section0003.xhtml";s:9:"playOrder";s:0:"";s:6:"childs";a:0:{}}}',
			$string);
	}
}
