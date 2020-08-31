<?php

namespace Litlife\Fb2Fix\Tests;

use DOMDocument;
use DOMXpath;
use Litlife\Fb2\Fb2Description;
use Litlife\Fb2\Fb2TitleInfo;
use Litlife\Fb2Fix\Fb2Fix;
use PHPUnit\Framework\TestCase;

class Fb2FixTest extends TestCase
{
	public function testFixWrongPrefixes()
	{
		$fb2Fix = new Fb2Fix();
		$fb2Fix->setContent(file_get_contents(__DIR__ . '/books/test_wrong_link_namespaces.fb2'));
		$fb2Fix->fixNamespacePrefix();

		$dom = new DOMDocument();
		$dom->loadXML($fb2Fix->getContent());

		$xpath = new DOMXpath($dom);

		$this->assertEquals('<image l:href="#image.png"/>',
			$dom->saveXML($xpath->query('//*[local-name()=\'image\']')->item(0)));

		$this->assertEquals('<a l:href="#section1">section1</a>',
			$dom->saveXML($xpath->query('//*[local-name()=\'a\']')->item(0)));
	}

	public function testParseNameSpace()
	{
		$fb2Fix = new Fb2Fix();
		$fb2Fix->setContent(file_get_contents(__DIR__ . '/books/test_wrong_link_namespaces.fb2'));

		$this->assertEquals('l', $fb2Fix->parseNamespacePrefix());
	}

	public function testBrokenTags()
	{
		$fb2Fix = new Fb2Fix();
		$fb2Fix->setContent(file_get_contents(__DIR__ . '/books/test_broken_tags.fb2'));
		$fb2Fix->fixBrokenTags();

		$dom = new DOMDocument();
		$dom->loadXML($fb2Fix->getContent());

		$xpath = new DOMXpath($dom);

		$this->assertEquals('<image l:href="#image.png"/>',
			$dom->saveXML($xpath->query('//*[local-name()=\'annotation\']')->item(0)));
	}


}
