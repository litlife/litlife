<?php

namespace App\Traits;

trait ExternalLinks
{
	public function getExternalLinksCount($text)
	{
		$count = 0;

		preg_replace_callback('/\<a(.+?)\>(.+?)\<\/a\>/iu', function ($array) use (&$count) {

			list ($string, $attributes, $text) = $array;

			if (preg_match('/(.*)href\=\"(.+?)\"(.*)/iu', $attributes, $matches)) {
				$url = $matches[2];

				if (preg_match('/^\/away(.*)/iu', $url)) {
					$count++;
				}
			}
		}, $text);

		return $count;
	}
}