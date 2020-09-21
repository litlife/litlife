<?php

namespace App\Traits;

use App\Library\BBCode\BBCode;
use Stevebauman\Purify\Facades\Purify;

trait BBCodeable
{
	public function setBBCode($value)
	{
		$value = mb_substr($value, 0, 1000000);
		$value = trim(replaceAsc194toAsc32($value));
		$value = removeJsAdCode($value);

		$html = (new BBCode)->toHtml($value);

		if (trim($html) == '')
			$html = null;

		$this->attributes[$this->getBBCodeColumnColumn()] = $value;
		$this->attributes[$this->getHtmlColumnColumn()] = $html;
	}

	public function setHtml($value)
	{
		$value = trim(replaceAsc194toAsc32($value));
		$value = removeJsAdCode($value);
		$value = preg_replace("/<br(\ *)\/?>(\ *)<br(\ *)\/?>/iu", "\n\n", $value);
		$this->attributes[$this->getHtmlColumnColumn()] = @Purify::clean($value);
	}

	public function getBBCodeColumnColumn()
	{
		return defined('static::BB_CODE_COLUMN') ? static::BB_CODE_COLUMN : 'bb_text';
	}

	public function getHtmlColumnColumn()
	{
		return defined('static::HTML_COLUMN') ? static::HTML_COLUMN : 'text';
	}

	public function setForbidTags($array)
	{
		$this->forbidBBCodeTags = $array;
	}

	public function getBBCode()
	{
		return $this->attributes[$this->getBBCodeColumnColumn()];
	}
}