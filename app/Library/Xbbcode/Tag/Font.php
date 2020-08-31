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

use App\Library\Xbbcode\Attributes;

/**
 * Class Font
 * Класс для тега [font]
 */
class Font extends Tag
{
	const BEHAVIOUR = 'span';

	/**
	 * Return html code
	 *
	 * @return string
	 */
	public function __toString()
	{
		if ($this->getAttributes()->count() < 1)
			return $this->getBody();

		if ($this->getAttributes()->count() == 1 and $this->getAttributes()->getAttributeValue('class') == 'bb')
			return $this->getBody();

		return '<span ' . $this->getAttributes() . '>' . $this->getBody() . '</span>';
	}

	/**
	 * @return Attributes
	 */
	protected function getAttributes()
	{
		$attr = parent::getAttributes();

		$face = '';

		if (isset($this->attributes['face']))
			$face = $this->attributes['face'];

		if (isset($this->attributes['font']))
			$face = $this->attributes['font'];

		$fonts = preg_split("/,[\ ]+/", $face);

		$fonts = array_map(function ($font) {
			$font = trim($font, '"\'');
			return preg_replace("/[\ ]+/iu", ' ', $font);
		}, $fonts);

		$available_fonts_to_lower = array_map('strtolower', config('litlife.available_fonts'));

		foreach ($fonts as $font) {
			if (in_array(mb_strtolower($font), $available_fonts_to_lower)) {
				$filtered_fonts[] = $font;
			}
		}

		foreach ($filtered_fonts as $c => $font) {
			if (preg_match('/ /iu', $font))
				$filtered_fonts[$c] = "'" . $font . "'";
			else
				$filtered_fonts[$c] = $font;
		}

		$face = implode(', ', $filtered_fonts ?? null);

		if ($face)
			$attr->set('style', 'font-family:' . $face . '');

		/*
				if (isset($this->attributes['color'])) {
					$attr->set('color', $this->attributes['color']);
				}

				if (isset($this->attributes['size'])) {
					$attr->set('size', $this->attributes['size']);
				}
				*/
		return $attr;
	}
}
