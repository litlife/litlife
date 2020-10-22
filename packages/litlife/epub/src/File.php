<?php

namespace Litlife\Epub;

class File
{
	protected $path;
	protected $content;
	protected $epub;

	function __construct(Epub &$epub, $path = null)
	{
		$this->epub = &$epub;

		if (!empty($path))
			$this->setPath($path);
	}

	public function getStream()
	{

	}

	public function getPathinfo()
	{
		return pathinfo($this->getPath());
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setPath($path)
	{
		$this->path = $path;
		$this->epub->files[$path] = $this;
	}

	public function getDirname()
	{
		return dirname($this->getPath());
	}

	public function getBaseName()
	{
		return basename($this->getPath());
	}

	public function getFileName()
	{
		return pathinfo($this->getPath(), PATHINFO_FILENAME);
	}

	public function getExtension()
	{
		return pathinfo($this->getPath(), PATHINFO_EXTENSION);
	}

	public function getSize()
	{
		return strlen($this->getContent());
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function isExists(): bool
	{
		return isset($this->epub->files[$this->getPath()]);
	}

	public function isFoundInZip(): bool
	{
		return $this->epub->zipFile->hasEntry($this->getPath());
	}

	public function delete()
	{
		//$this->epub->zipFile->deleteFromName($this->path);
		unset($this->epub->files[$this->getPath()]);
	}

	public function getMd5()
	{
		return md5($this->getContent());
	}

	public function rename(string $newName)
	{
		$from = (string)$this->getPath();

		$this->epub->zipFile->rename($this->getPath(), $newName);

		$this->setPath($newName);
	}

	public function save()
	{
		$this->epub->zipFile->addFromString($this->getPath(), $this->getContent());
	}

	public function loadContent()
	{
		$this->content = $this->epub->zipFile->getEntryContents($this->path);
	}

	public function writeInArchive()
	{
		$this->epub->zipFile->addFromString($this->getPath(), $this->content);
	}

	public function getEpub(): Epub
	{
		return $this->epub;
	}
}