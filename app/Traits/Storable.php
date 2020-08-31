<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Litlife\Url\Url;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

trait Storable
{
	private $zipFile;

	protected static function bootStorable()
	{
		static::creating(function ($query) {
			if (empty($query->attributes['storage']))
				$query->attributes['storage'] = config('filesystems.default');
		});
	}

	public function setStorageAttribute($storage)
	{
		$this->attributes['storage'] = $storage;
	}

	public function setNameAttribute($name)
	{
		$name = fileNameFormat($name);

		$url = Url::fromString($name);

		$name = $url->getFilename();

		if ($url->getExtension()) {
			$name .= '.' . mb_strtolower($url->getExtension());
		}

		$this->attributes['name'] = $name;
	}

	public function getUrlAttribute()
	{
		return Storage::disk($this->storage)
			->url($this->dirname . '/' . rawurlencode($this->name));
	}

	public function getFullUrlWithScheme($scheme = 'https'): string
	{
		$url = Url::fromString($this->url);

		if (empty($url->getHost()))
			return (string)$url;
		else
			return (string)$url->withScheme($scheme);
	}

	public function fileExists()
	{
		if (Storage::disk($this['storage'])->exists($this->dirname . '/' . $this->name))
			return true;
		else
			return false;
	}

	public function getContents()
	{
		return Storage::disk($this->storage)->get($this->dirname . '/' . $this->name);
	}

	public function getZipFile()
	{
		return $this->zipFile;
	}

	public function getStreamOrFirstFileInArchiveStream()
	{
		if ($this->isZipArchive()) {
			return $this->getFirstFileInArchiveStream();
		}

		return $this->getStream();
	}

	public function isZipArchive()
	{
		if ($this->getMimeType() == 'application/zip') {
			try {
				$this->zipFile = new ZipFile();
				$this->zipFile->openFromStream($this->getStream());

				if ($this->zipFile->hasEntry('mimetype')) {
					if (trim($this->zipFile->getEntryContents('mimetype')) != 'application/zip') {
						return false;
					}
				}

			} catch (ZipException $exception) {
				return false;
			}

			return true;
		} else
			return false;
	}

	public function getMimeType()
	{
		return Storage::disk($this['storage'])
			->mimeType($this->dirname . '/' . $this->name);
	}

	public function getStream()
	{
		return Storage::disk($this['storage'])
			->getDriver()
			->readStream($this->dirname . '/' . $this->name);
	}

	public function getFirstFileInArchiveStream()
	{
		if (empty($this->zipFile)) {
			$this->zipFile = new ZipFile();
			$this->zipFile->openFromStream($this->getStream());
		}

		$files = $this->zipFile->getListFiles();

		$tmp = tmpfile();
		fwrite($tmp, $this->zipFile->getEntryContents($files[0]));
		rewind($tmp);

		return $tmp;
	}

	public function rename($new_name)
	{
		$old_name = $this->name;

		if ($this->isZipArchive()) {
			$archived_file_name = $this->getFirstFileInArchive();

			if (Url::fromString($new_name)->getExtension() == 'zip')
				$new_archived_file_name = Url::fromString($new_name)->getFilename();
			else
				$new_archived_file_name = $new_name;

			$this->zipFile->rename($archived_file_name, $new_archived_file_name);

			Storage::disk($this->storage)
				->put($this->dirname . '/' . $old_name, $this->zipFile->outputAsString());

			unset($this->zipFile);
		}

		DB::beginTransaction();

		if ($new_name != $old_name) {
			$this->name = $new_name;

			$tmpName = uniqid();

			Storage::disk($this->storage)
				->move($this->dirname . '/' . $old_name, $this->dirname . '/' . $tmpName);

			Storage::disk($this->storage)
				->move($this->dirname . '/' . $tmpName, $this->dirname . '/' . $this->name);
		}

		$this->save();

		DB::commit();
	}

	public function getFirstFileInArchive()
	{
		if (empty($this->zipFile)) {
			$this->zipFile = new ZipFile();
			$this->zipFile->openFromStream($this->getStream());
		}

		$files = $this->zipFile->getListFiles();

		if (empty($files))
			throw new ZipException('Not a single file was found in the archive');

		return $files[0];
	}

	public function moveToStorage($storage)
	{
		if (!$this->exists())
			throw new FileNotFoundException();

		$size = $this->getSize();
		$old_storage = $this->storage;

		if (Storage::disk($storage)->exists($this->dirname . '/' . $this->name))
			throw new Exception('File already exists in storage ' . $storage);

		$resource = $this->getStream();

		Storage::disk($storage)
			->put($this->dirname . '/' . $this->name, $resource);

		if (!Storage::disk($storage)->exists($this->dirname . '/' . $this->name))
			throw new Exception('Error file copy to ' . $storage);

		if ($size != Storage::disk($storage)->size($this->dirname . '/' . $this->name))
			throw new Exception('File size does not match');

		$this->storage = $storage;
		$this->save();

		fclose($resource);

		if (!Storage::disk($old_storage)
			->delete($this->dirname . '/' . $this->name))
			throw new Exception('The file is not deleted');
	}

	public function exists()
	{
		if (Storage::disk($this['storage'])->exists($this->dirname . '/' . $this->name))
			return true;
		else
			return false;
	}

	public function getSize()
	{
		return Storage::disk($this['storage'])
			->size($this->dirname . '/' . $this->name);
	}
}