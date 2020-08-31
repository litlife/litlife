<?php

namespace App\Observers;

use App\BookGroup;

class BookGroupObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param BookGroup $group
	 * @return void
	 */

	public function deleted(BookGroup $group)
	{
		foreach ($group->books()->any()->get() as $book) {
			$book->removeFromGroup();
			$book->save();
		}
	}
}