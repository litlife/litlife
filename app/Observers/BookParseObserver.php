<?php

namespace App\Observers;

use App\BookParse;

class BookParseObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param BookParse $bookParse
	 * @return void
	 */
	public function creating(BookParse $bookParse)
	{

	}

	public function updating(BookParse $bookParse)
	{

	}
}