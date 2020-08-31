<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ConvertToUtf8Strings extends TransformsRequest
{
	/**
	 * Transform the given value.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	protected function transform($key, $value)
	{
		mb_substitute_character(0x20);

		return is_string($value) ? mb_convert_encoding($value, "UTF-8") : $value;
	}
}
