<?php

namespace Litlife\Url\Exceptions;

class InvalidArgument extends \Spatie\Url\Exceptions\InvalidArgument
{
	public static function failedParseUrl(string $url): self
	{
		return new static("Url `{$url}` parse failed");
	}
}
