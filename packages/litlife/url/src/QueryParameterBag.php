<?php

namespace Litlife\Url;

class QueryParameterBag
{
	/** @var array */
	protected $parameters;

	public function __construct(array $parameters = [])
	{
		$this->parameters = $parameters;
	}

	public static function fromString(string $query = '')
	{
		if ($query === '') {
			return new static();
		}

		parse_str($query, $array);

		return new static($array);
	}

	public function __toString()
	{
		$string = http_build_query($this->all(), null);

		$string = preg_replace('/(%5B)\d+(%5D=)/iu', '$1$2', $string);

		return $string;
	}

	public function all(): array
	{
		return $this->parameters;
	}

	public function get(string $key, $default = null)
	{
		return $this->parameters[$key] ?? $default;
	}

	public function has(string $key): bool
	{
		return array_key_exists($key, $this->parameters);
	}

	public function set(string $key, string $value)
	{
		$this->parameters[$key] = $value;

		return $this;
	}

	public function unset(string $key)
	{
		unset($this->parameters[$key]);

		return $this;
	}
}
