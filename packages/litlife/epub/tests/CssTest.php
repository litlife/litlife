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
		$css->loadCss('.content {}');
		$css->setPath('OEBPS/Styles/main.css');

		$epub->opf()->appendToManifest('main.css', 'Styles/main.css', 'text/css');

		$string = $epub->outputAsString();

		$epub = new Epub;
		$epub->setFile($string);

		$this->assertInstanceOf(Css::class, $epub->getFileByPath('OEBPS/Styles/main.css'));
		$this->assertEquals('.content {}', $epub->getFileByPath('OEBPS/Styles/main.css')->getContent());
	}
}
