<?php

namespace App\Traits;

use App\Image;
use Litlife\Url\Url;

trait ImageableTrait
{
	public function images()
	{
		return $this->morphToMany('App\Images', 'imageable');
	}

	public function replaceToID()
	{
		// TODO доделать поиск изображения в текстах
		$this->bb_text = preg_replace_callback('/\[img(?:\=([0-9]+)x([0-9]+))?\](.+?)\[\/img\]/iu',
			[$this, 'tagImg'], $this->bb_text);
	}

	public function tagImg($array)
	{
		list ($string, $width, $height, $url) = $array;

		$url = trim($url);

		$url = Url::fromString($url);

		if ((!empty($url->getHost())) and (!in_array($url->getHost(), config('litlife.site_hosts')))) {

			preg_match('/^(.*)\_(?:[a-z0-9]{2})\.' . $url->getExtension() . '$/iu', $url->getBasename(), $matches);

			if ($matches > 0) {
				$image = Image::where('storage', 'old')
					->where('dirname', $url->getDirname())
					->where('name', '~', '^' . $matches[1] . '_(?:[a-z0-9]{2})\.' . $url->getExtension() . '$')
					->first();

				return '[img=' . $width . 'x' . $height . ']' . $image->id . '[/img]';
			} else {
				/*
								preg_match('/^' . preg_quote(config('filesystems.default')) . '$/iu', $url->getBasename(), $matches)


								$image = Image::where('storage', '!=', 'old')
									->where('dirname', $url->getDirname())
									->where('name', $url->getBasename())
									->first();

								return '[img=' . $width . 'x' . $height . ']' . $image->id . '[/img]';
								*/
			}
		}
	}
}