<?php

namespace Litlife\Fb2Fix;

use function tidy_parse_string;

class Fb2Fix
{
	public $dom;
	private $tidy;

	public function setContent($content)
	{
		$config = [
			'input-xml' => TRUE,
			'output-xml' => TRUE
		];

		$this->tidy = tidy_parse_string($content, $config);
	}

	public function fixBrokenTags()
	{
		$this->tidy->cleanRepair();
	}

	public function getContent()
	{
		return $this->tidy;
	}

	public function fixNamespacePrefix()
	{
		$rightPrefix = $this->parseNamespacePrefix();

		$this->tidy = preg_replace_callback('/\<a\ (.*)([A-z0-9]+)\:href\=\"(.*)\"(.*)\>(.*)\<\/a\>/siuU', function ($m) use ($rightPrefix) {
			return '<a ' . $m[1] . $rightPrefix . ':href="' . $m[3] . '"' . $m[4] . '>' . $m[5] . '</a>';
		}, $this->tidy);

		$this->tidy = preg_replace_callback('/\<image\ (.*)([A-z0-9]+)\:href\=\"(.*)\"(.*)\/\>/siuU', function ($m) use ($rightPrefix) {
			return '<image ' . $m[1] . $rightPrefix . ':href="' . $m[3] . '"' . $m[4] . '/>';
		}, $this->tidy);
	}

	public function parseNamespacePrefix()
	{
		$pattern = '/\<FictionBook(?:.*)xmlns\:([A-z]+)\=(?:.*)\"http\:\/\/www\.w3\.org\/1999\/xlink\"(?:.*)\>/siuU';

		preg_match($pattern, $this->tidy, $matches);

		$prefix = trim($matches[1]);

		if (!empty($prefix))
			return $prefix;
		else
			return false;
	}
}
