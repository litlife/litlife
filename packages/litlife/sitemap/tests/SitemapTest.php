<?php

namespace Litlife\Sitemap\Tests;

use Litlife\Sitemap\Sitemap;
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase
{
	public function testAddUrl()
	{
		$sitemap = new Sitemap();

		$this->assertEquals(0, $sitemap->getUrlCount());

		$sitemap->addUrl('https://www.sitemaps.org/ru/protocol.html?dfgdfg=sdf&dfgdfg=dfg', '2005-01-01', 'monthly', '0.8');

		$this->assertEquals(1, $sitemap->getUrlCount());
		$this->assertEquals(174, $sitemap->getSize());

		$sitemap->addUrl('https://www.sitemaps.org/ru/protocol2.html', '2005-01-01', 'monthly', '0.8');

		$this->assertEquals(2, $sitemap->getUrlCount());
		$this->assertEquals(323, $sitemap->getSize());

		$this->assertNotNull($sitemap->getContent());
	}

	public function testSetGetMaxSitemapPartUrlCount()
	{
		$count = rand(100, 100000);

		$sitemap = new Sitemap();
		$sitemap->setMaxUrlCount($count);

		$this->assertEquals($count, $sitemap->getMaxUrlCount());
	}

	public function testSetGetMaxSitemapPartLength()
	{
		$count = rand(100, 100000);

		$sitemap = new Sitemap();
		$sitemap->setMaxSize($count);

		$this->assertEquals($count, $sitemap->getMaxSize());
	}

	public function testIsCountOfURLsIsGreaterThanMax()
	{
		$sitemap = new Sitemap();
		$sitemap->setMaxUrlCount(100);
		$sitemap->setUrlCount(10);

		$this->assertFalse($sitemap->isCountOfURLsIsGreaterOrEqualsThanMax());

		$sitemap->setMaxUrlCount(100);
		$sitemap->setUrlCount(100);

		$this->assertTrue($sitemap->isCountOfURLsIsGreaterOrEqualsThanMax());

		$sitemap->setMaxUrlCount(100);
		$sitemap->setUrlCount(101);

		$this->assertTrue($sitemap->isCountOfURLsIsGreaterOrEqualsThanMax());
	}

	public function testIsSizeLargerThanMax()
	{
		$sitemap = new Sitemap();
		$sitemap->setMaxSize(100);
		$sitemap->setSize(10);

		$this->assertFalse($sitemap->isSizeLargerOrEqualsThanMax());

		$sitemap->setMaxSize(100);
		$sitemap->setSize(100);

		$this->assertTrue($sitemap->isSizeLargerOrEqualsThanMax());

		$sitemap->setMaxSize(100);
		$sitemap->setSize(101);

		$this->assertTrue($sitemap->isSizeLargerOrEqualsThanMax());
	}

	public function testOpen()
	{
		$sitemap = new Sitemap();

		$sitemap->addUrl('https://www.sitemaps.org/ru/protocol.html', '2005-01-01', 'monthly', '0.8');
		$sitemap->addUrl('https://www.sitemaps.org/ru/protocol2.html', '2006-01-01T12:45:31+03:00', 'daily', '0.9');

		$this->assertEquals(2, $sitemap->getUrlCount());
		$this->assertEquals(310, $sitemap->getSize());

		$content = $sitemap->getContent();

		$sitemap = new Sitemap();
		$sitemap->open($content);

		$urls = $sitemap->getUrls();

		$this->assertEquals('https://www.sitemaps.org/ru/protocol.html', $urls[0]['location']);
		$this->assertEquals('2005-01-01', $urls[0]['lastmod']);
		$this->assertEquals('monthly', $urls[0]['changefreq']);
		$this->assertEquals('0.8', $urls[0]['priority']);

		$this->assertEquals('https://www.sitemaps.org/ru/protocol2.html', $urls[1]['location']);
		$this->assertEquals('2006-01-01T12:45:31+03:00', $urls[1]['lastmod']);
		$this->assertEquals('daily', $urls[1]['changefreq']);
		$this->assertEquals('0.9', $urls[1]['priority']);

		$this->assertEquals(2, $sitemap->getUrlCount());
		$this->assertEquals(310, $sitemap->getSize());
	}

	public function testSpecialChars()
	{
		$url = 'https://www.sitemaps.org/ru/protocol.html?test=test&test2=test';

		$sitemap = new Sitemap();
		$sitemap->addUrl($url, '2005-01-01', 'monthly', '0.8');
		$content = $sitemap->getContent();

		$sitemap = new Sitemap();
		$sitemap->open($content);
		$urls = $sitemap->getUrls();

		$this->assertEquals('https://www.sitemaps.org/ru/protocol.html?test=test&amp;test2=test', $urls[0]['location']);
		$this->assertEquals('2005-01-01', $urls[0]['lastmod']);
		$this->assertEquals('monthly', $urls[0]['changefreq']);
		$this->assertEquals('0.8', $urls[0]['priority']);
	}

	public function testGetWhereLocation()
	{
		$sitemap = new Sitemap();

		$sitemap->addUrl('https://www.sitemaps.org/ru/protocol.html', '2005-01-01', 'monthly', '0.8');
		$sitemap->addUrl('https://www.sitemaps.org/ru/protocol2.html', '2006-01-01T12:45:31+03:00', 'daily', '0.9');

		$url = $sitemap->getWhereLocation('https://www.sitemaps.org/ru/protocol2.html');

		$this->assertNotNull($url);
		$this->assertEquals('2006-01-01T12:45:31+03:00', $url['lastmod']);
	}

	public function testEmptyLastmodChangefreqPriority()
	{
		$url = 'https://www.sitemaps.org/ru/protocol.html?test=test&test2=test';

		$sitemap = new Sitemap();
		$sitemap->addUrl($url);
		$content = $sitemap->getContent();

		$sitemap = new Sitemap();
		$sitemap->open($content);

		$this->assertEquals(1, $sitemap->getUrlCount());
	}
	/*
		public function testTime()
		{
			$count = 50000;

			$sitemap = new Sitemap();

			for ($a = 0; $a < $count; $a++)
			{
				$sitemap->addUrl('https://www.sitemaps.org/ru/protocol.html', '2005-01-01', 'monthly', '0.8');
			}

			$this->assertEquals($count, $sitemap->getUrlCount());

			$content = $sitemap->getContent();

			$sitemap = new Sitemap();
			$sitemap->open($content);

			$this->assertEquals($count, $sitemap->getUrlCount());
		}
		*/
}
