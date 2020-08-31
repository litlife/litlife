<?php

namespace Litlife\Sitemap\Tests;

use DOMDocument;
use DOMXpath;
use Litlife\Sitemap\SitemapIndex;
use PHPUnit\Framework\TestCase;

class SitemapIndexTest extends TestCase
{
	public function testCreate()
	{
		$index = new SitemapIndex();
		$index->addSitemap('http://www.example.com/sitemap1.xml.gz', '2004-10-01T18:23:17+00:00');

		$dom = new DOMDocument();
		$dom->loadXML($index->getContent());

		$this->assertEquals("1.0",
			$dom->version);

		$this->assertEquals("UTF-8",
			$dom->encoding);

		$this->assertEquals("sitemapindex",
			$dom->documentElement->nodeName);

		$this->assertEquals("http://www.w3.org/2001/XMLSchema-instance",
			$dom->documentElement->getAttributeNS("http://www.w3.org/2000/xmlns/", 'xsi'));

		$this->assertEquals("http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemapindex.xsd",
			$dom->documentElement->getAttributeNS("http://www.w3.org/2001/XMLSchema-instance", 'schemaLocation'));
	}

	public function testGetSitemapsCount()
	{
		$index = new SitemapIndex();

		$index->addSitemap('http://www.example.com/sitemap1.xml.gz', '2004-10-01T18:23:17+00:00');

		$this->assertEquals(1, $index->getSitemapsCount());

		$index->addSitemap('http://www.example.com/sitemap2.xml.gz', '2004-10-01T18:23:17+00:00');

		$this->assertEquals(2, $index->getSitemapsCount());
	}

	public function testAddSitemap()
	{
		$index = new SitemapIndex();

		$index->addSitemap('http://www.example.com/sitemap1.xml.gz', '2004-10-01T18:23:17+00:00');
		$index->addSitemap('http://www.example.com/sitemap2.xml.gz', '2005-10-01T18:23:17+00:00');

		$dom = new DOMDocument();
		$dom->loadXML($index->getContent());

		$xpath = new DOMXpath($dom);

		$sitemap = $xpath->query("*[local-name()='sitemap']", $dom->documentElement)->item(0);

		$this->assertEquals('http://www.example.com/sitemap1.xml.gz',
			$xpath->query("*[local-name()='loc']", $sitemap)->item(0)->nodeValue);

		$this->assertEquals('2004-10-01T18:23:17+00:00',
			$xpath->query("*[local-name()='lastmod']", $sitemap)->item(0)->nodeValue);

		$sitemap = $xpath->query("*[local-name()='sitemap']", $dom->documentElement)->item(1);

		$this->assertEquals('http://www.example.com/sitemap2.xml.gz',
			$xpath->query("*[local-name()='loc']", $sitemap)->item(0)->nodeValue);

		$this->assertEquals('2005-10-01T18:23:17+00:00',
			$xpath->query("*[local-name()='lastmod']", $sitemap)->item(0)->nodeValue);
	}

	public function testOpen()
	{
		$index = new SitemapIndex();
		$index->addSitemap('http://www.example.com/sitemap1.xml.gz', '2004-10-01T18:23:17+00:00');
		$string = $index->getContent();

		$index = new SitemapIndex();
		$index->open($string);

		$this->assertEquals(1, $index->getSitemapsCount());

		$lastSitemap = $index->getLastSitemap();

		$this->assertEquals('http://www.example.com/sitemap1.xml.gz', $lastSitemap['location']);
		$this->assertEquals('2004-10-01T18:23:17+00:00', $lastSitemap['lastmod']);
	}

	public function getLastSitemap()
	{
		$index = new SitemapIndex();

		$index->addSitemap('http://www.example.com/sitemap1.xml.gz', '2004-10-01T18:23:17+00:00');
		$index->addSitemap('http://www.example.com/sitemap2.xml.gz', '2005-10-01T18:23:17+00:00');

		$lastSitemap = $index->getLastSitemap();

		$this->assertEquals('http://www.example.com/sitemap2.xml.gz', $lastSitemap['location']);
	}
}
