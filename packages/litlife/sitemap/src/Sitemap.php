<?php

namespace Litlife\Sitemap;

use Carbon\Carbon;
use DOMDocument;
use DOMXpath;
use Exception;

class Sitemap
{
	private $xml;
	private $size = 0;
	private $urlCount = 0;
	private $maxSize = 49500000;
	private $maxUrlCount = 49500;
	private $urls = [];

	public function open($string)
	{
		$dom = new DOMDocument("1.0", "UTF-8");
		$dom->loadXml($string, LIBXML_PARSEHUGE);

		$xpath = new DOMXpath($dom);

		foreach ($xpath->query("*[local-name()='url']", $dom->documentElement) as $url) {

			$nodes = $xpath->query("*[local-name()='loc']", $url);
			if ($nodes->length > 0)
				$location = $nodes->item(0)->nodeValue;

			$nodes = $xpath->query("*[local-name()='lastmod']", $url);
			if ($nodes->length > 0)
				$lastmod = $nodes->item(0)->nodeValue;

			$nodes = $xpath->query("*[local-name()='changefreq']", $url);
			if ($nodes->length > 0)
				$changefreq = $nodes->item(0)->nodeValue;

			$nodes = $xpath->query("*[local-name()='priority']", $url);
			if ($nodes->length > 0)
				$priority = $nodes->item(0)->nodeValue;

			$this->addUrl(
				$location,
				$lastmod ?? null,
				$changefreq ?? null,
				$priority ?? null
			);
		}
	}

	public function addUrl($location, $lastmod = null, $changefreq = 'weekly', $priority = '0.5'): bool
	{
		$length = 0;
		$url = array();

		$url['location'] = trim(htmlentities($location));
		$length += mb_strlen($url['location']);

		if (!empty($lastmod)) {

			if (is_object($lastmod))
				$lastmod = $lastmod->toW3cString();
			else
				$lastmod = Carbon::parse($lastmod)
					->toW3cString();

			$lastmod = str_replace('T00:00:00+00:00', '', $lastmod);

			$url['lastmod'] = trim(htmlentities($lastmod));
			$length += mb_strlen($url['lastmod']);
		}

		if (!empty($changefreq)) {

			if (!in_array($changefreq, ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never']))
				throw new Exception('changefreq wrong value:"' . $changefreq . '"');

			$url['changefreq'] = trim(htmlentities($changefreq));
			$length += mb_strlen($url['changefreq']);
		}

		if (!empty($priority)) {

			if (!preg_match('/([0,1]{1})\.([0-9]{1})/iu', $priority))
				throw new Exception('priority wrong value: "' . $priority . '"');

			$url['priority'] = trim(htmlentities($priority));
			$length += mb_strlen($url['priority']);
		}

		$this->setSize($this->getSize() + $length + 87);
		$this->setUrlCount($this->getUrlCount() + 1);

		$this->urls[] = $url;

		return true;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function setSize($number)
	{
		$this->size = $number;
	}

	public function getUrlCount(): int
	{
		return $this->urlCount;
	}

	public function setUrlCount($number)
	{
		$this->urlCount = $number;
	}

	public function getContent()
	{
		$xml = new DOMDocument("1.0", "UTF-8");
		$xml->preserveWhiteSpace = false;
		$xml->formatOutput = true;

		$s = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
		$s .= '<urlset';
		$s .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
		$s .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"';
		$s .= ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
		$s .= '>';

		foreach ($this->getUrls() as $url) {

			$s .= '<url>';
			$s .= '<loc>' . $url['location'] . '</loc>';

			if (!empty($url['lastmod']))
				$s .= '<lastmod>' . $url['lastmod'] . '</lastmod>';

			if (!empty($url['changefreq']))
				$s .= '<changefreq>' . $url['changefreq'] . '</changefreq>';

			if (!empty($url['priority']))
				$s .= '<priority>' . $url['priority'] . '</priority>';

			$s .= '</url>';

		}

		$s .= '</urlset>';

		$xml->loadXML($s);

		if (!$xml->schemaValidate(__DIR__ . '/../xsd/sitemap.xsd'))
			throw new Exception('Sitemap invalid');

		return $xml->saveXML();
	}

	public function getUrls(): array
	{
		return $this->urls;
	}

	public function getUrlsCount(): int
	{
		return count($this->urls);
	}

	public function isCountOfURLsIsGreaterOrEqualsThanMax(): bool
	{
		return $this->getUrlCount() >= $this->getMaxUrlCount();
	}

	public function getMaxUrlCount(): int
	{
		return $this->maxUrlCount;
	}

	public function setMaxUrlCount($count)
	{
		$this->maxUrlCount = $count;
	}

	public function isSizeLargerOrEqualsThanMax(): bool
	{
		return $this->getSize() >= $this->getMaxSize();
	}

	public function getMaxSize(): int
	{
		return $this->maxSize;
	}

	public function setMaxSize($size)
	{
		$this->maxSize = $size;
	}

	public function mustBeValid()
	{
		if (!$this->isValid())
			throw new Exception('Sitemap invalid');

		return true;
	}

	public function isValid(): bool
	{
		return $this->xml->schemaValidate(__DIR__ . '/../xsd/sitemap.xsd');
	}

	public function getWhereLocation($location)
	{
		foreach ($this->urls as $url) {
			if ($url['location'] == $location)
				return $url;
		}

		return false;
	}

	/*
		public function addUrl($location, $lastmod = null, $changefreq = 'weekly', $priority = '0.5') :bool
		{
			$this->xml = new \DOMDocument('1.0', 'utf-8');
			$this->urlset = $this->xml->createElementNS($this->xmlns, "urlset");

			$url = $this->xml->createElementNS($this->xmlns, "url");

			if (!empty($location)) {
				$location_node = $this->xml->createElementNS($this->xmlns, "loc");
				$location_node->appendChild($this->xml->createTextNode($location));
				$url->appendChild($location_node);
			}

			if (!empty($lastmod)) {

				if (is_object($lastmod))
					$lastmod = $lastmod->toW3cString();
				else
					$lastmod = Carbon::parse($lastmod)
						->toW3cString();

				$lastmod_node = $this->xml->createElementNS($this->xmlns, "lastmod", $lastmod);
				$url->appendChild($lastmod_node);
			}

			if (!empty($changefreq)) {

				if (!in_array($changefreq, ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never']))
					throw new \Exception('changefreq wrong value:"' . $changefreq . '"');

				$changefreq_node = $this->xml->createElementNS($this->xmlns, "changefreq", $changefreq);
				$url->appendChild($changefreq_node);
			}

			if (!empty($priority)) {

				if (!preg_match('/([0,1]{1})\.([0-9]{1})/iu', $priority))
					throw new \Exception('priority wrong value: "' . $priority . '"');

				$priority_node = $this->xml->createElementNS($this->xmlns, "priority", $priority);
				$url->appendChild($priority_node);
			}

			$this->urlset->appendChild($url);

			dd($this->xml->saveXML($url));

			$length = mb_strlen($this->xml->saveXML($url));

			$this->setSize($this->getSize() + $length);
			$this->setUrlCount($this->getUrlCount() + 1);

			return true;
		}
	*/
}