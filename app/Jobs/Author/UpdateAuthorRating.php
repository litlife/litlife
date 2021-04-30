<?php

namespace App\Jobs\Author;

use App\Author;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateAuthorRating
{
	use Dispatchable;

	protected $author;

	/**
	 * Create a new job instance.
	 *
	 * @param Author $author
	 * @return void
	 */
	public function __construct(Author $author)
	{
		$this->author = $author;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$query = $this->author->any_books()
			->acceptedAndSentForReview()
			->notConnected();

		$this->author->votes_count = (clone $query)
			->join('book_votes', 'books.id', '=', 'book_votes.book_id')
            ->whereNull('book_votes.deleted_at')
			->count();

		$this->author->vote_average = (clone $query)
			->where('vote_average', '>', '0')
			->avg("vote_average");

		$this->author->rating = intval($this->author->votes_count * $this->author->vote_average);
		$this->author->rating_changed = false;
		$this->author->save();

		$queryJoined = (clone $query)->join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id');

		$this->author->averageRatingForPeriod->day_rating = $queryJoined->sum('day_rating');
		$this->author->averageRatingForPeriod->week_rating = $queryJoined->sum('week_rating');
		$this->author->averageRatingForPeriod->month_rating = $queryJoined->sum('month_rating');
		$this->author->averageRatingForPeriod->quarter_rating = $queryJoined->sum('quarter_rating');
		$this->author->averageRatingForPeriod->year_rating = $queryJoined->sum('year_rating');
		$this->author->averageRatingForPeriod->all_rating = $this->author->rating;
		$this->author->push();
	}
}
