<?php

namespace Litlife\Epub;

use Imagick;
use ImagickException;
use Litlife\Url\Url;

class Image extends File
{
	protected $epub;
	protected $content;
	private $imagick;
	private $id;
	private $href;

	function __construct(&$epub, string $path = null)
	{
		parent::__construct($epub, $path);

		if (!empty($path)) {
			$this->content = $this->epub->zipFile->getEntryContents($this->path);
		}
	}

	public function isValid()
	{
		try {
			$this->getImagick();
		} catch (ImagickException $exception) {
			return false;
		}
		return true;
	}

	public function getImagick()
	{
		if (empty($this->imagick)) {
			$this->imagick = new Imagick();
			$this->imagick->readImageBlob($this->content);
		}

		return $this->imagick;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getWidth()
	{
		return $this->getImagick()->getImageWidth();
	}

	public function getHeight()
	{
		return $this->getImagick()->getImageHeight();
	}

	public function guessExtension()
	{
		return strtolower($this->getImagick()->getImageFormat());
	}

	public function addToManifest()
	{
		$this->epub->opf()
			->appendToManifest($this->getId(), $this->getHref(), $this->getContentType());
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getHref()
	{
		return $this->href;
	}

	public function setHref($href)
	{
		$this->href = $href;
	}

	public function getContentType()
	{
		return $this->getImagick()->getImageMimeType();
	}

	public function rename(string $newName)
	{
		$oldPath = $this->getPath();

		$newPath = (string)Url::fromString($this->getPath())->withBasename($newName);

		foreach ($this->epub->getSectionsList() as $number => &$section) {

			$imagesNodes = $section->xpath()->query("//*[local-name()='img'][@src]", $section->body());

			foreach ($imagesNodes as $imagesNode) {

				$src = $imagesNode->getAttribute('src');

				$image_url = Url::fromString($src);

				if ($this->getPath() == $image_url->getPathRelativelyToAnotherUrl($section->getPath())->withoutFragment()) {
					$imagesNode->setAttribute('src', $image_url->withBasename($newName));
				}
			}
		}

		$query = "*[local-name()='item'][@media-type][@href][contains(@media-type,'image')]";

		foreach ($this->epub->opf()->xpath()->query($query, $this->epub->opf()->manifest()) as $c => $node) {

			$image_url = Url::fromString($node->getAttribute('href'));

			if ($this->getPath() == $image_url->getPathRelativelyToAnotherUrl($this->epub->opf()->getPath())->withoutFragment()) {

				$node->setAttribute('href', $image_url->withBasename($newName));
				$node->setAttribute('id', $newName);
			}
		}

		$this->epub->files[$newPath] = &$this;

		$this->setPath($newPath);

		unset($this->epub->files[$oldPath]);

		return true;
	}

	public function setPath($path)
	{
		$this->path = $path;
		$this->epub->files[$path] = $this;
	}
}