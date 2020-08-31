<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ReplaceAsc194ToSpace extends TransformsRequest
{
	/**
	 * The names of the attributes that should not be trimmed.
	 *
	 * @var array
	 */
	protected $except = [

	];

	protected function transform($key, $value)
	{
		if (in_array($key, $this->except, true)) {
			return $value;
		}

		return is_string($value) ? replaceAsc194toAsc32($value) : $value;
	}
}
