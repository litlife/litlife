<?php

/******************************************************************************
 *                                                                            *
 *   Copyright (C) 2006-2007  Dmitriy Skorobogatov  dima@pc.uz                *
 *                                                                            *
 *   This program is free software; you can redistribute it and/or modify     *
 *   it under the terms of the GNU General Public License as published by     *
 *   the Free Software Foundation; either version 2 of the License, or        *
 *   (at your option) any later version.                                      *
 *                                                                            *
 *   This program is distributed in the hope that it will be useful,          *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 *   GNU General Public License for more details.                             *
 *                                                                            *
 *   You should have received a copy of the GNU General Public License        *
 *   along with this program; if not, write to the Free Software              *
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA *
 *                                                                            *
 ******************************************************************************/

namespace App\Library\Xbbcode\Tag;

use Litlife\Url\Exceptions\InvalidArgument;
use Litlife\Url\Url;
use Xbbcode\Attributes;
use Xbbcode\Tag\Tag;


/**
 * Class Img
 * Класс для тега [img]
 */
class Img extends Tag
{
	const BEHAVIOUR = 'img';
	public $base64_image = false;

	/**
	 * Return html code
	 *
	 * @return string
	 */
	public function __toString()
	{
		$attributes = $this->getAttributes();

		if (empty($attributes->getAttributeValue('data-src')))
			return '';

		if ($this->base64_image) {
			$html = '';
			$html .= '<img class="bb" ';
			$html .= 'src="' . htmlentities($attributes->getAttributeValue('data-src')) . '"';
			$html .= '/>';
		} else {
			$fullUrl = Url::fromString($attributes->getAttributeValue('data-src'))
				->withoutQueryParameter('w')
				->withoutQueryParameter('h');

			$html = '';
			$html .= '<a target="_blank" href="' . htmlentities($fullUrl) . '">';
			$html .= '<img class="bb lazyload" ' . $this->getAttributes() . ' ';
			$html .= 'srcset="';
			$html .= '' . htmlentities($fullUrl->withQueryParameter('w', '400')->withQueryParameter('q', '85')) . ' 400w,';
			$html .= '' . htmlentities($fullUrl->withQueryParameter('w', '700')->withQueryParameter('q', '80')) . ' 700w,';
			$html .= '' . htmlentities($fullUrl->withQueryParameter('w', '1000')->withQueryParameter('q', '75')) . ' 1000w';
			$html .= '" ';
			$html .= 'sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px" ';
			$html .= 'src="' . htmlentities($fullUrl) . '"';
			$html .= ' />';
			$html .= '</a>';
		}

		return $html;
	}

	/**
	 * @return Attributes
	 */
	protected function getAttributes()
	{
		$attr = parent::getAttributes();

		$src = $this->getSrc();

		if ($src) {

			try {
				$url = urldecode(urldecode($src));

				$basename = rawurlencode(Url::fromString($url)->getBasename());
				$url = Url::fromString($url)->withBasename($basename);

				$attr->set('data-src', (string)$url);

			} catch (InvalidArgument $exception) {

			}

			if (preg_match('/^data\:([A-z\/]+)\;base64\,(.*)$/iu', $src, $matches)) {
				if (base64_decode($matches[2]) != false) {
					$attr->set('data-src', (string)$src);
					$this->base64_image = true;
				}
			}
		}

		//dd($this->attributes);
		if (!empty($this->attributes['img'])) {
			if (preg_match('/([0-9]+)(x|х)([0-9]+)/iu', $this->attributes['img'], $mathes)) {
				$width = $mathes[1];
				$height = $mathes[3];

				if ($this->isValidSize($width)) {
					$attr->set('width', $width);
				}

				if ($this->isValidSize($height)) {
					$attr->set('height', $height);
				}
			}
		}

		$alt = '';
		if (isset($this->attributes['alt'])) {
			$alt = $this->attributes['alt'];
		}
		if (!$alt && isset($this->attributes['title'])) {
			$alt = $this->attributes['title'];
		}
		$attr->set('alt', $alt); // обязательный

		if (isset($this->attributes['title'])) {
			$attr->set('title', $this->attributes['title']);
		}

		if (isset($this->attributes['width'])) {
			if ($this->isValidSize($this->attributes['width'])) {
				$attr->set('width', $this->attributes['width']);
			}
		}

		if (isset($this->attributes['height'])) {
			if ($this->isValidSize($this->attributes['height'])) {
				$attr->set('height', $this->attributes['height']);
			}
		}

		if (isset($this->attributes['border'])) {
			if ($this->isValidNumber($this->attributes['border'])) {
				$attr->set('border', $this->attributes['border']);
			}
		}

		return $attr;
	}

	/**
	 * @return string
	 */
	protected function getSrc()
	{
		$href = '';
		if (isset($this->attributes['url'])) {
			$href = $this->attributes['url'];
		}
		if (!$href && isset($this->attributes['data-src'])) {
			$href = $this->attributes['data-src'];
		}

		if (!$href) {
			$href = $this->getTreeText();
		}

		return $href;
	}
}
