<?php

namespace App\Library;

use Illuminate\Support\Facades\Storage;

class TempFolders
{
	private $folders = [];
	private $storagePath;

	function __construct()
	{
		$this->storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
	}

	function create()
	{
		$path = 'temp/' . uniqid();

		Storage::disk('local')->makeDirectory($path);

		$path = $this->storagePath . $path;

		$this->folders[] = $path;

		return $path;
	}

	function purge()
	{
		foreach ($this->folders as $folder) {
			$path = mb_substr($folder, strlen($this->storagePath));
			Storage::disk('local')->deleteDirectory($path);
		}
	}
}

?>