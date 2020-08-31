<?php

namespace App\Observers;

use App\CollectedBook;
use App\Collection;

class CollectedBookObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param CollectedBook $collectedBook
	 * @return void
	 */
	public function creating(CollectedBook $collectedBook)
	{
		$collectedBook->autoAssociateAuthUser();
	}

	public function created(CollectedBook $collectedBook)
	{
		$this->refreshBooksCount($collectedBook->collection);
	}

	public function refreshBooksCount(Collection $collection)
	{
		$collection->refreshBooksCount();
		$collection->save();
	}

	public function deleted(CollectedBook $collectedBook)
	{
		$this->refreshBooksCount($collectedBook->collection);
	}

	public function restored(CollectedBook $collectedBook)
	{
		$this->refreshBooksCount($collectedBook->collection);
	}
}