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
		$this->path = $path;
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
		if (empty($this->content))
			$this->content = $this->epub->zipFile->getEntryContents($this->path);

		return $this->content;
	}

	public function setContent($content)
	{
		if ($this->isExists())
			$this->delete();

		$this->content = $content;

		$this->epub->zipFile->addFromString($this->path, $content);
	}

	public function isExists()
	{
		if (!$this->epub->zipFile->hasEntry($this->getPath()))
			return false;
		else
			return true;
	}

	public function delete()
	{
		$this->epub->zipFile->deleteFromName($this->path);
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
}