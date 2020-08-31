<?php

namespace Litlife\Sitemap;

use Carbon\Carbon;
use DOMDocument;
use DOMXpath;
use Exception;

class SitemapIndex
{
	public $storage = 'public';
	public $xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9';
	private $sitemaps = [];

	public function __construct()
	{

	}

	public function open($string)
	{
		$dom = new DOMDocument("1.0", "UTF-8");
		$dom->loadXml($string);

		$sitemapindex = $dom->documentElement;

		$xpath = new DOMXpath($dom);

		foreach ($xpath->query("*[local-name()='sitemap']", $sitemapindex) as $sitemap) {
			$this->sitemaps[] = [
				'location' => $xpath->query("*[local-name()='loc']", $sitemap)->item(0)->nodeValue,
				'lastmod' => $xpath->query("*[local-name()='lastmod']", $sitemap)->item(0)->nodeValue
			];
		}
	}

	public function getContent()
	{
		$dom = new DOMDocument("1.0", "UTF-8");

		$sitemapindex = $dom->createElementNS($this->xmlns, 'sitemapindex');

		$sitemapindex->setAttributeNS("http://www.w3.org/2000/xmlns/",
			'xmlns:xsi',
			"http://www.w3.org/2001/XMLSchema-instance");

		$sitemapindex->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',
			'schemaLocation',
			'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemapindex.xsd');

		$dom->appendChild($sitemapindex);

		foreach ($this->sitemaps as $sitemap) {
			$sitemapNode = $dom->createElementNS($this->xmlns, "sitemap");

			if (!empty($sitemap['location'])) {
				$location_node = $dom->createElementNS($this->xmlns, "loc", $sitemap['location']);
				$sitemapNode->appendChild($location_node);
			}

			if (!empty($sitemap['lastmod'])) {

				if (is_object($sitemap['lastmod']))
					$lastmod = $sitemap['lastmod']->toW3cString();
				else
					$lastmod = Carbon::parse($sitemap['lastmod'])
						->toW3cString();

				$lastmod_node = $dom->createElementNS($this->xmlns, "lastmod", $lastmod);
				$sitemapNode->appendChild($lastmod_node);
			}

			$sitemapindex->appendChild($sitemapNode);
		}

		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;

		if (!$dom->schemaValidate(__DIR__ . '/../xsd/siteindex.xsd'))
			throw new Exception('Sitemapindex invalid');

		return $dom->saveXML();
	}

	public function addSitemap($location, $lastmod = null): bool
	{
		$this->sitemaps[] = [
			'location' => $location,
			'lastmod' => $lastmod
		];

		return true;
	}

	public function getSitemapsCount(): int
	{
		return count($this->sitemaps);
	}

	public function getSitemaps(): array
	{
		return $this->sitemaps;
	}

	public function getLastSitemap()
	{
		return end($this->sitemaps);
	}
}
