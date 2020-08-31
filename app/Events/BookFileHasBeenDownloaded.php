<?php

namespace App\Events;

use App\BookFile;
use Illuminate\Foundation\Events\Dispatchable;

class BookFileHasBeenDownloaded
{
	use Dispatchable;

	public $bookFile;

	/**
	 * Create a new event instance.
	 *
	 * @param BookFile $bookFile
	 * @return void
	 */
	public function __construct(BookFile $bookFile)
	{
		$this->bookFile = $bookFile;
	}

}
