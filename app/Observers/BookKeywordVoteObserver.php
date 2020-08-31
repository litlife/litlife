<?php

namespace App\Observers;

use App\BookKeywordVote;
use App\Jobs\UpdateBookKeywordVotes;

class BookKeywordVoteObserver
{
	public function created(BookKeywordVote $vote)
	{
		$vote->autoAssociateAuthUser();

		UpdateBookKeywordVotes::dispatch($vote->book_keyword);
	}

	public function updated(BookKeywordVote $vote)
	{
		if ($vote->isChanged('vote'))
			UpdateBookKeywordVotes::dispatch($vote->book_keyword);
	}

	public function deleted(BookKeywordVote $vote)
	{
		UpdateBookKeywordVotes::dispatch($vote->book_keyword);
	}

	public function restored(BookKeywordVote $vote)
	{
		UpdateBookKeywordVotes::dispatch($vote->book_keyword);
	}
}