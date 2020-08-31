<?php

namespace App\Traits;

trait CharactersCountTrait
{
	public function setCharactersCountAttribute($value)
	{
		$this->attributes[$this->getCharactersCountColumn()] = $value;
	}

	public function getCharactersCountColumn()
	{
		return defined('static::CHARACTERS_COUNT') ? static::CHARACTERS_COUNT : 'characters_count';
	}

	public function getCharactersCountAttribute($value)
	{
		return intval($value);
	}

	public function refreshCharactersCount()
	{
		$this->{$this->getCharactersCountColumn()} = $this->getCharacterCountInText($this->getContent());
	}

	public function getCharacterCountInText($text)
	{
		return transform($text, function ($text) {

			$text = strip_tags($text);

			$text = preg_replace("/[[:space:]]+/iu", "", $text);

			$text = mb_strlen($text);

			return $text;
		});
	}

	public function getUpperCaseLettersPercent($text)
	{
		if ($this->{$this->getCharactersCountColumn()} < 1)
			return 0;

		return round((100 / $this->{$this->getCharactersCountColumn()}) * $this->getUpperCaseCharactersCount($text));
	}

	public function getUpperCaseCharactersCount($text)
	{
		return mb_strlen(preg_replace('/[^[:upper:]]+/u', '', strip_tags($text)));
	}
}