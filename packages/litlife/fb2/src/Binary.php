<?php

namespace Litlife\Fb2;

use Imagick;
use ImagickException;

class Binary
{
	private $blob;
	private $id;
	private $content_type;
	private $imagick;
	private $fb2;

	public function __construct(&$fb2, $id, $content_type = null)
	{
		$this->fb2 = $fb2;
		$this->id = urldecode($id);
		$this->content_type = $content_type;
		$this->fb2->binaries[$id] = $this;
	}

	public function setContentAsBase64($base64)
	{
		$this->blob = base64_decode($base64);
	}

	public function getContentAsBase64()
	{
		return base64_encode($this->blob);
	}

	public function getId()
	{
		return $this->id;
	}

	public function getContentType()
	{
		return $this->content_type;
	}

	public function open($blob)
	{
		$this->blob = $blob;

		$this->content_type = $this->getImagick()->getImageMimeType();
	}

	public function getImagick(): Imagick
	{
		if (empty($this->imagick)) {
			$this->imagick = new Imagick();
			$this->imagick->readImageBlob($this->getContent());
		}

		return $this->imagick;
	}

	public function getContent()
	{
		return $this->blob;
	}

	public function isValidImage()
	{
		try {
			if ($this->getImagick()->valid())
				return true;

		} catch (ImagickException $exception) {
		}

		return false;
	}
}
