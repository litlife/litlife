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

use Xbbcode\Attributes;
use Xbbcode\Tag\Tag;

/**
 * Class Spoiler
 * Класс для тегов [spoiler] и [hide]
 */
class Spoiler extends Tag
{
	const BR_LEFT = 1;
	const BR_RIGHT = 1;
	public $showButton = 'Показать спойлер';
	public $hideButton = 'Скрыть спойлер';

	/**
	 * Return html code
	 *
	 * @return string
	 */
	public function __toString()
	{
		$attr = $this->getAttributes();
		$id = $attr->getAttributeValue('id');

		$title = empty($this->attributes['spoiler']) ? '' : $this->attributes['spoiler'];

		//return $this->getSpoiler($id) . '<div ' . $attr . '>' . $this->getBody() . '</div>';

		return '<div class="bb_spoiler"><div class="bb_spoiler_title">' . $title . '</div><div class="bb_spoiler_text">' . $this->getBody() . '</div></div>';
	}

	/**
	 * @return Attributes
	 */
	protected function getAttributes()
	{
		$attr = parent::getAttributes();

		$attr->add('class', 'bb_spoiler');
		$attr->set('style', 'display: none');

		$id = uniqid('spoiler');
		$attr->set('id', $id);

		return $attr;
	}

	/**
	 * @param string $id
	 * @return string
	 */
	protected function getSpoiler($id)
	{
		//dd($this->attributes['spoiler']);

		$title = empty($this->attributes['spoiler']) ? '' : $this->attributes['spoiler'];

		return ' <div class="bb_spoiler_button" value="' . htmlspecialchars(
				$this->showButton
			) . '" onclick="var node = document.getElementById(\'' . $id . '\'); (node.style.display == \'none\' ? (node.style.display = \'block\', this.value = \'' . htmlspecialchars(
				$this->hideButton,
				ENT_QUOTES
			) . '\') : (node.style.display = \'none\', this.value = \'' . htmlspecialchars(
				$this->showButton,
				ENT_QUOTES
			) . '\'));" /> ' . $title . ' </div>';
	}
}
