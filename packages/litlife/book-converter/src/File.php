<?php

namespace Litlife\BookConverter;

use Litlife\Url\Url;

class File
{
	private $file;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function getPath(): string
	{
		return (string)$this->file;
	}

	public function getFilePath(): string
	{
		return (string)$this->file;
	}

	public function getStream()
	{
		return fopen($this->file, 'r+b');
	}

	public function getFileStream()
	{
		return $this->getStream();
	}

	public function getExtension(): string
	{
		return mb_strtolower(Url::fromString($this->file)
			->getExtension());
	}

	public function putContentsFromResource($resource): bool
	{
		rewind($resource);

		if (file_put_contents($this->file, $resource) < 1)
			throw new \RuntimeException('Zero bytes were written to the file');

		return true;
	}

	public function getSize(): int
	{
		return filesize($this->file);
	}
}