<?php

namespace Litlife\Url;

use Exception;
use Litlife\Url\Exceptions\InvalidArgument;
use Spatie\Macroable\Macroable;

class Url
{
	use Macroable;

	const VALID_SCHEMES = ['http', 'https', 'mailto'];
	/** @var string */
	protected $scheme = '';
	/** @var string */
	protected $host = '';
	/** @var int|null */
	protected $port = null;
	/** @var string */
	protected $user = '';
	/** @var string|null */
	protected $password = null;
	/** @var string */
	protected $path = '';
	/** @var \Spatie\Url\QueryParameterBag */
	protected $query;
	/** @var string */
	protected $fragment = '';
	protected $dirname = '';
	protected $filename = '';
	protected $extension = '';

	public function __construct()
	{
		$this->query = new QueryParameterBag();
	}

	public static function create()
	{
		return new static();
	}

	public function getSegmentsWithoutBaseName(): array
	{
		$segments = $this->getSegments();

		if (end($segments) == $this->getBasename())
			$segments = array_slice($segments, 0, -1);

		return $segments;
	}

	public function getSegments(): array
	{
		return explode('/', trim($this->path, '/'));
	}

	public function getBasename(): string
	{
		return $this->getSegment(-1);
	}

	public function getSegment(int $index, $default = null)
	{
		$segments = $this->getSegments();

		if ($index === 0) {
			throw InvalidArgument::segmentZeroDoesNotExist();
		}

		if ($index < 0) {
			$segments = array_reverse($segments);
			$index = abs($index);
		}

		return $segments[$index - 1] ?? $default;
	}

	public function getPathInfo()
	{
		return pathinfo($this->path);
	}

	public function withoutFragment()
	{
		$url = clone $this;

		$url->fragment = '';

		return $url;
	}

	public function withoutQuery()
	{
		$url = clone $this;

		foreach ($url->getAllQueryParameters() as $key) {
			$url->query->unset($key);
		}

		return $url;
	}

	public function getAllQueryParameters(): array
	{
		return $this->query->all();
	}

	public function appendToFilename($append)
	{
		return $this->withFilename($this->getFilename() . $append);
	}

	public function withFilename($s)
	{
		if ($this->getExtension() == '')
			return $this->withBasename($s);
		else
			return $this->withBasename($s)->withExtension($this->getExtension());
	}

	public function getExtension(): string
	{
		return trim(pathinfo($this->getBasename(), PATHINFO_EXTENSION));
	}

	public function withBasename(string $basename)
	{
		$basename = trim($basename, '/');

		if ($this->getDirname() === '/') {
			return $this->withPath('/' . $basename);
		}

		if ($this->getDirname() == '')
			return $this->withPath($basename);
		else
			return $this->withPath($this->getDirname() . '/' . $basename);
	}

	public function getDirname(): string
	{
		$dirname = pathinfo($this->path, PATHINFO_DIRNAME);
		if ($dirname == '.')
			return '';
		else
			return trim($dirname);
	}

	public function withPath($path)
	{
		$url = clone $this;

		$url->path = $path;

		return $url;
	}

	public function withExtension($extension)
	{
		$extension = trim($extension);

		if ($extension == '')
			throw new Exception('Extension is not set');

		return $this->withBasename($this->getFilename() . '.' . $extension);
	}

	public function getFilename(): string
	{
		return trim(pathinfo($this->getBasename(), PATHINFO_FILENAME));
	}

	public function getPathRelativelyToAnotherUrl($absoluteFilePath)
	{
		$absolute = Url::fromString($absoluteFilePath);
		$relative = clone $this;

		if (mb_substr($relative->getDirname(), 0, 2) == './') {

			return $relative->withDirname($absolute->getDirname() . '/' . mb_substr($relative->getDirname(), 2));

		} elseif ($relative->getDirnameArrayIndex(0) == '..') {

			$length = 0;

			foreach ($relative->getDirnameArray() as $segment) {
				if ($segment == '..')
					$length++;
			}

			$absoluteDirArrayPart = array_slice($absolute->getDirnameArray(), 0, intval('-' . $length));

			$relativeDirArray = array_slice($relative->getDirnameArray(), $length);

			$relativeDirArray = array_merge($absoluteDirArrayPart, $relativeDirArray);

			return $relative->withDirnameArray($relativeDirArray);

		} elseif (count($absolute->getDirnameArray()) > 0) {

			$relativeDirArray = array_merge($absolute->getDirnameArray(), $relative->getDirnameArray());
			//$relativeDirArray = array_filter($relativeDirArray);
			return $relative->withDirnameArray($relativeDirArray);
		}

		return $relative;
	}

	public static function fromString(string $url)
	{
		$array = parse_url($url);

		if ($array === false)
			throw InvalidArgument::failedParseUrl($url);

		if (is_array($array))
			$parts = array_merge($array);

		$url = new static();
		$url->scheme = isset($parts['scheme']) ? $url->sanitizeScheme($parts['scheme']) : '';
		$url->host = $parts['host'] ?? '';
		$url->port = $parts['port'] ?? null;
		$url->user = $parts['user'] ?? '';
		$url->password = $parts['pass'] ?? null;
		$url->path = $parts['path'] ?? '';
		$url->query = QueryParameterBag::fromString($parts['query'] ?? '');
		$url->fragment = $parts['fragment'] ?? '';

		$path_parts = pathinfo($url->path);
		$url->dirname = $path_parts['dirname'] ?? '';
		$url->filename = $path_parts['filename'] ?? '';
		$url->extension = $path_parts['extension'] ?? '';

		return $url;
	}

	protected function sanitizeScheme(string $scheme): string
	{
		$scheme = strtolower($scheme);

		if (!in_array($scheme, static::VALID_SCHEMES)) {
			throw InvalidArgument::invalidScheme($scheme);
		}

		return $scheme;
	}

	public function withDirname(string $dirname)
	{
		if (!$this->getBasename()) {
			return $this->withPath($dirname);
		}

		if (empty($dirname))
			return $this->withPath($this->getBasename());
		else {
			return $this->withPath($dirname . '/' . $this->getBasename());
		}
	}

	public function getDirnameArrayIndex($index)
	{
		$array = $this->getDirnameArray();
		if (isset($array[$index]))
			return $array[$index];
		else
			return null;
	}

	public function getDirnameArray()
	{
		if ($this->getDirname() == '')
			return [];

		$array = explode('/', $this->getDirname());

		return $array;
	}

	public function withDirnameArray($array)
	{
		$url = clone $this;

		$dirname = implode('/', $array);

		if (isset($array[0]) and $array[0] == '')
			$dirname = '/' . $dirname;

		return $url->withDirname($dirname);
	}

	public function getRelativePathUrl($absolutePath)
	{
		$absolute = Url::fromString($absolutePath);
		$relative = clone $this;

		$array = [];

		foreach ($relative->getDirnameArrayWithoutEmpty() as $number => $segment) {
			if (isset($absolute->getDirnameArrayWithoutEmpty()[$number]) and $absolute->getDirnameArrayWithoutEmpty()[$number] == $segment) {
				$array[] = $segment;
			}
		}

		if (count($array)) {
			$array_slice = array_slice($relative->getDirnameArrayWithoutEmpty(), count($array));

			return $relative->withDirname(implode($array_slice, '/'));
		}

		$array = $absolute->getDirnameArrayWithoutEmpty();
		$count = count($array);

		if ($count > 0) {
			$path = implode('/', array_fill(0, $count, '..'));

			return $relative->withDirname($path . $relative->getDirname());
		}
	}

	public function getDirnameArrayWithoutEmpty()
	{
		$array = $this->getDirnameArray();

		$new_array = [];

		foreach ($array as $segment) {
			if (trim($segment) != '')
				$new_array[] = $segment;
		}

		return $new_array;
	}

	public function __toString()
	{
		$url = '';

		if ($this->getScheme() !== '' && $this->getScheme() != 'mailto') {
			$url .= $this->getScheme() . '://';
		}

		if ($this->getScheme() === 'mailto' && $this->getPath() !== '') {
			$url .= $this->getScheme() . ':';
		}

		if ($this->getScheme() === '' && $this->getAuthority() !== '') {
			$url .= '//';
		}

		if ($this->getAuthority() !== '') {
			$url .= $this->getAuthority();
		}

		$url .= $this->getPath();

		if ($this->getQuery() !== '') {

			if ($this->getPath() === '')
				$url .= '/';

			$url .= '?' . $this->getQuery();
		}

		if ($this->getFragment() !== '') {

			if ($this->getPath() === '' and !empty($this->getHost()))
				$url .= '/';

			$url .= '#' . $this->getFragment();
		}

		return $url;
	}

	public function getScheme()
	{
		return $this->scheme;
	}

	public function getPath(): string
	{
		return preg_replace('/\/+/iu', '/', $this->path);
	}

	public function getAuthority()
	{
		$authority = $this->host;

		if ($this->getUserInfo()) {
			$authority = $this->getUserInfo() . '@' . $authority;
		}

		if ($this->port !== null) {
			$authority .= ':' . $this->port;
		}

		return $authority;
	}

	public function getUserInfo()
	{
		$userInfo = $this->user;

		if ($this->password !== null) {
			$userInfo .= ':' . $this->password;
		}

		return $userInfo;
	}

	public function getQuery(): string
	{
		return $this->query->__toString();
	}

	public function getFragment()
	{
		return $this->fragment;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPathQueryFragment()
	{
		$url = clone $this;

		$url->scheme = '';
		$url->host = '';
		$url->port = null;
		$url->user = '';
		$url->password = null;

		if (trim($url->getPath(), '/') == '')
			return $url->withDirname('/');
		else
			return $url;
	}

	public function urlencode()
	{
		$url = clone $this;

		foreach ($url->getSegmentsArray() as $segment) {
			$segments[] = urlencode($segment);
		}

		return $url->withPath(implode('/', $segments));
	}

	public function getSegmentsArray()
	{
		$array = explode('/', $this->getPath());

		return $array;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function getQueryParameter(string $key, $default = null)
	{
		return $this->query->get($key, $default);
	}

	public function hasQueryParameter(string $key): bool
	{
		return $this->query->has($key);
	}

	public function withQueryParameter(string $key, string $value)
	{
		$url = clone $this;
		$url->query->unset($key);

		$url->query->set($key, $value);

		return $url;
	}

	public function withoutQueryParameter(string $key)
	{
		$url = clone $this;
		$url->query->unset($key);

		return $url;
	}

	public function getFirstSegment()
	{
		$segments = $this->getSegments();

		return $segments[0] ?? null;
	}

	public function getLastSegment()
	{
		$segments = $this->getSegments();

		return end($segments) ?? null;
	}

	public function withScheme($scheme)
	{
		$url = clone $this;

		$url->scheme = $this->sanitizeScheme($scheme);

		return $url;
	}

	public function withUserInfo($user, $password = null)
	{
		$url = clone $this;

		$url->user = $user;
		$url->password = $password;

		return $url;
	}

	public function withHost($host)
	{
		$url = clone $this;

		$url->host = $host;

		return $url;
	}

	public function withPort($port)
	{
		$url = clone $this;

		$url->port = $port;

		return $url;
	}

	public function withQuery($query)
	{
		$url = clone $this;

		$url->query = QueryParameterBag::fromString($query);

		return $url;
	}

	public function withFragment($fragment)
	{
		$url = clone $this;

		$url->fragment = $fragment;

		return $url;
	}

	public function matches(self $url): bool
	{
		return (string)$this === (string)$url;
	}

	public function __clone()
	{
		$this->query = clone $this->query;
	}
}