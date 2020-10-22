<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Css;
use Litlife\Epub\Epub;

class CssTest extends TestCase
{
	public function test()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$css = new Css($epub);
		$css->setPath('OEBPS/Styles/main.css');
		$css->setContent('.content {}');

		$this->assertEquals($epub->getAllFilesList(), $css->getEpub()->getAllFilesList());

		$this->assertTrue($css->isExists());

		$epub->opf()->appendToManifest('main.css', 'Styles/main.css', 'text/css');

		$string = $epub->outputAsString();

		$epub = new Epub;
		$epub->setFile($string);
		$css = $epub->getFileByPath('OEBPS/Styles/main.css');

		$this->assertTrue($css->isFoundInZip());

		$this->assertInstanceOf(Css::class, $css);
		$this->assertEquals('.content {}', $css->getContent());
	}
}
