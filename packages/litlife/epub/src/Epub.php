<?php

namespace Litlife\Epub;

use Exception;
use Litlife\Url\Url;
use PhpZip\ZipFile;
use ZipArchive;

class Epub
{
	public $opf;
	public $ncx;
	public $zipFile;
	public $default_opf_path;
	public $default_ncx_path;
	public $default_container_path;
	public $default_folder;
	public $files = [];
	public $container;
	private $unifyTagIds;

	function __construct()
	{
		$this->zipFile = new ZipFile;
		$this->default_folder = 'OEBPS';
		$this->default_container_path = 'META-INF/container.xml';
		$this->default_opf_path = $this->default_folder . '/content.opf';
		$this->default_ncx_path = $this->default_folder . '/toc.ncx';
	}

	public function setFile($path, $flags = null)
	{
		$this->files = [];

		if (is_resource($path))
			$this->zipFile->openFromStream($path);
		elseif (strlen($path) < 255 and file_exists($path))
			$this->zipFile->openFile($path);
		else
			$this->zipFile->openFromString($path);

		$fullPath = $this->container()
			->rootfiles()
			->getElementsByTagName('rootfile')
			->item(0)
			->getAttribute('full-path');

		if (!empty($fullPath)) {
			$this->opf = new Opf($this, $fullPath);
		}

		$this->loadFiles();
	}

	public function container(): ?Container
	{
		if (empty($this->container)) {
			$files = $this->findFiles('/META\-INF\/container\.xml/iu');

			if (empty($files[0])) {
				return null;
			} else {
				$this->container = new Container($this, $files[0]);
				$this->files[$files[0]] = $this->container;
			}
		}

		return $this->container;
	}

	/**
	 * Возвращаем все пути к файлам совпадающие с регулярным выражением
	 *
	 * @param string $pattern
	 * @return array $files
	 */

	public function findFiles(string $pattern)
	{
		$matcher = $this->zipFile->matcher();

		foreach ($matcher->all()->getMatches() as $file) {
			if (preg_match($pattern, $file, $matches)) {
				$files[] = $file;
			}
		}

		return empty($files) ? [] : $files;
	}

	public function loadFiles()
	{
		$query = "//*[local-name()='item'][@href]";

		foreach ($this->opf()->xpath()->query($query, $this->opf()->spine()) as $node) {

			$id = $node->getAttribute('id');

			$mimeType = $node->getAttribute('media-type');

			$href = urldecode($node->getAttribute('href'));

			$fullPath = (string)Url::fromString($href)
				->getPathRelativelyToAnotherUrl($this->opf()->getPath())
				->withoutFragment();

			if ($mimeType == 'application/xhtml+xml') {
				$section = new Section($this, $fullPath);

				if ($itemref = $this->opf()->xpath()->query('//*[local-name()=\'itemref\'][@idref="' . $id . '"]', $this->opf()->spine())->item(0)) {
					$linear = trim($itemref->getAttribute('linear'));

					if (!empty($linear))
						$section->setLinear($linear);
				}

				$this->files[$fullPath] = $section;

			} elseif ($mimeType == 'application/x-dtbncx+xml') {
				$this->files[$fullPath] = new Ncx($this, $fullPath);
			} elseif (preg_match('/image\/([[:alpha:]]+)/iu', $mimeType)) {
				$this->files[$fullPath] = new Image($this, $fullPath);
			} elseif ($mimeType == 'text/css') {
				$this->files[$fullPath] = new Css($this, $fullPath);
			} else {
				$this->files[$fullPath] = new File($this, $fullPath);
			}
		}
	}

	public function opf(): ?Opf
	{
		if (empty($this->opf)) {

			$files = $this->findFiles('/([[:graph:]]+)\.opf/iu');

			if (empty($files[0])) {
				return null;
			} else
				$this->opf = new Opf($this, $this->default_opf_path);
		}

		return $this->opf;
	}

	public function getNcxFullPath()
	{
		if (!empty($this->opf())) {
			$item = $this->opf()->xpath()
				->query('//*[@media-type="application/x-dtbncx+xml"]', $this->opf()->manifest())
				->item(0);

			if (!empty($item)) {
				$href = $item->getAttribute('href');

				return (string)Url::fromString($href)->getPathRelativelyToAnotherUrl($this->opf()->getPath());
			}
		}

		return false;
	}

	public function createOpf($path = null): Opf
	{
		if (empty($path))
			$path = $this->default_opf_path;

		$opf = new Opf($this);
		$opf->setPath($this->default_opf_path);

		$this->container()->appendRootFile($this->default_opf_path, "application/oebps-package+xml");

		return $opf;
	}

	public function createContainer($path = null): Container
	{
		if (empty($path))
			$path = $this->default_opf_path;

		$container = new Container($this);
		$container->setPath($this->default_container_path);

		return $container;
	}

	public function createNcx($path = null): Ncx
	{
		if (empty($path))
			$path = $this->default_ncx_path;

		$ncx = new Ncx($this);
		$ncx->setPath($path);

		$href = Url::fromString($path)->getRelativePathUrl($this->opf()->getPath());

		$this->opf()->appendToManifest('ncx', $href, 'application/x-dtbncx+xml');

		return $ncx;
	}

	public function ncx(): ?Ncx
	{
		foreach ($this->files as $file) {
			if ($file instanceof Ncx)
				return $file;
		}

		return null;
	}

	public function getSectionsList(): array
	{
		$sections = [];

		foreach ($this->files as $path => $item) {
			if ($item instanceof Section) {
				$sections[$path] = $item;
			}
		}

		return $sections;
	}

	public function getSectionsListInOrder(): array
	{
		$sections = [];

		foreach ($this->opf()->xpath()->query("//*[local-name()='itemref']", $this->opf()->spine()) as $item) {
			$idref = $item->getAttribute('idref');

			$href = $this->opf()
				->getManifestItemById($idref)
				->item(0)
				->getAttribute('href');

			$fullPath = (string)Url::fromString($href)
				->getPathRelativelyToAnotherUrl($this->opf()->getPath())
				->withoutFragment();

			$sections[] = $this->files[$fullPath];
		}

		return $sections;
	}

	public function getImages(): array
	{
		$images = [];

		foreach ($this->files as $path => $item) {
			if ($item instanceof Image)
				$images[$path] = $item;
		}

		return $images;
	}

	public function getFirstFoundFile(string $pattern)
	{
		$files = $this->findFiles($pattern);

		if (!count($files))
			throw new Exception('Can not find file with ' . $pattern . ' pattern');

		return $files[0];
	}

	public function unifyTagIds()
	{
		if (!isset($this->unifyTagIds)) {

			$this->unifyTagIds = new UnifyTagIds($this, 'u-');
		}

		return $this->unifyTagIds;
	}

	public function unifyImagesNames()
	{
		if (!isset($this->unifyImagesNames)) {

			$this->unifyImagesNames = new UnifyImagesNames($this);
		}

		return $this->unifyImagesNames;
	}

	public function addExtensionIfNotExist()
	{
		if (!isset($this->addExtensionIfNotExist)) {

			$this->addExtensionIfNotExist = new AddExtensionIfNotExist($this);
		}

		return $this->addExtensionIfNotExist;
	}

	public function addSectionsIds()
	{
		if (!isset($this->addSectionsIds)) {

			$this->addSectionsIds = new AddSectionsIds($this);
		}

		return $this->addSectionsIds;
	}

	public function getSectionByFilePath($id)
	{
		if (isset($this->files[$id]))
			return $this->files[$id];
		else
			return null;
	}

	public function getImageByFilePath($id)
	{
		if (isset($this->files[$id]))
			return $this->files[$id];
		else
			return null;
	}

	public function getFileByPath($path)
	{
		if (isset($this->files[$path]))
			return $this->files[$path];
		else
			return null;
	}

	public function getAllFilesList()
	{
		return array_keys($this->files);
	}

	public function outputAsString($storeMethod = 'ZipArchive')
	{
		if ($storeMethod == 'ZipArchive') {
			$tmp = tmpfile();
			$fileName = stream_get_meta_data($tmp)['uri'];

			$zip = new ZipArchive;
			$zip->open($fileName);
			$zip->addFromString('mimetype', 'application/epub+zip');
			$zip->setCompressionName('mimetype', ZipArchive::CM_STORE);

			foreach ($this->files as $path => $file) {
				$zip->addFromString($path, $file->getContent());
			}

			$zip->close();

			return file_get_contents($fileName);
		} elseif ($storeMethod == 'ZipFile') {
			$zipFile = new ZipFile;
			$zipFile->setCompressionLevel(ZipFile::LEVEL_BEST_COMPRESSION);
			$zipFile->addFromString('mimetype', 'application/epub+zip', ZipFile::METHOD_STORED);

			foreach ($this->files as $path => $file) {
				$zipFile->addFromString($path, $file->getContent());
			}

			return $zipFile->outputAsString();
		}
	}
}
