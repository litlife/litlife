<?php

namespace App\Library\Xbbcode\Tag;

use Xbbcode\Attributes;
use Xbbcode\Tag\Tag;


/**
 * Class Quote
 * Класс для тегов [quote] и [blockquote]
 */
class Quote extends Tag
{
	const BR_LEFT = 1;
	const BR_RIGHT = 1;

	/**
	 * Return html code
	 *
	 * @return string
	 */
	public function __toString()
	{
		return '<blockquote ' . $this->getAttributes() . '>' . $this->getAuthor() . $this->getBody() . '</blockquote>';
	}

	/**
	 * @return Attributes
	 */
	protected function getAttributes()
	{
		$attr = parent::getAttributes();

		$attr->add('class', 'bb_quote');

		return $attr;
	}

	/**
	 * @return string
	 */
	protected function getAuthor()
	{
		$author = '';

		if (isset($this->attributes['author'])) {
			$author = $this->attributes['author'];
		}
		if (!$author && isset($this->attributes['quote'])) {
			$author = $this->attributes['quote'];
		}
		if (!$author && isset($this->attributes['blockquote'])) {
			$author = $this->attributes['blockquote'];
		}

		if ($author) {
			return '<div class="bb_quote_author">' . htmlspecialchars($author, ENT_NOQUOTES) . ':</div>';
		}

		return '';
	}
}
