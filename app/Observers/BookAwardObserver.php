<?php

namespace App\Observers;

use App\BookAward;
use App\Events\BookFilesCountChanged;

class BookAwardObserver
{
	public function creating(BookAward $bookAward)
	{
		$bookAward->autoAssociateAuthUser();
	}

	public function created(BookAward $bookAward)
	{
		$bookAward->book->updateAwardsCount();
	}

	public function deleted(BookAward $bookAward)
	{
		$bookAward->book->updateAwardsCount();
	}

	public function restored(BookAward $bookAward)
	{
		$bookAward->book->updateAwardsCount();
	}

}