<?php

namespace App\Jobs\Book;

use App\Book;
use App\BookVote;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class UpdateBookRating
{
	use Dispatchable;

	protected $book;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @return void
	 */
	public function __construct(Book $book)
	{
		$this->book = $book;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			if ($this->book->isInGroup()) {
				if ($this->book->isMainInGroup()) {
					$this->updateRating();

					foreach ($this->book->groupedBooks as $book) {
						$this->copyRating($this->book, $book);
					}
				} elseif (!empty($this->book->mainBook))
					$this->copyRating($this->book->mainBook, $this->book);
			} else {
				$this->updateRating();
			}
		});
	}

	public function updateRating()
	{
		$this->book->vote_average = floatval($this->book->votes()->has('create_user')->avg('vote'));
		$this->book->user_vote_count = intval($this->book->votes()->has('create_user')->count());

		if ($this->book->user_vote_count >= 50)
			$this->book->in_rating = 1;
		else
			$this->book->in_rating = 0;

		$vote_counts = BookVote::select(DB::raw('count(*) as count, vote'))
			->where("book_id", $this->book->id)
			->groupBy("vote")
			->orderBy("vote", "desc")
			->has('create_user')
			->get();

		$vote_counts_ar = [];

		foreach ($vote_counts as $vote_count) {
			$vote_counts_ar[$vote_count['vote']] = $vote_count['count'];
		}

		$this->book->rate_info = count($vote_counts_ar) ? $vote_counts_ar : null;

		$query = User::whereHas('votes', function (Builder $query) {
			$query->where('book_id', $this->book->id);
		});

		$this->book->male_vote_count = intval((clone $query)->male()->count());
		$this->book->female_vote_count = intval((clone $query)->female()->count());

		if (($this->book->male_vote_count > 0) or ($this->book->female_vote_count > 0)) {
			$this->book->male_vote_percent = round((($this->book->male_vote_count * 100) / ($this->book->male_vote_count + $this->book->female_vote_count)), 4);
		}

		$this->book->refresh_rating = false;
		$this->book->save();

		$this->day_rating();
		$this->week_rating();
		$this->month_rating();
		$this->quarter_rating();
		$this->year_rating();
		$this->all_rating();

		$this->book->average_rating_for_period->save();

		foreach ($this->book->authors()->get() as $author) {
			$author->rating_changed = true;
			$author->save();
		}

		$this->book->refresh();
	}

	public function day_rating()
	{
		$query = $this->book->votes()->where('created_at', '>', now()->subDay());
		$this->book->average_rating_for_period->day_vote_average = floatval($query->avg('vote'));
		$this->book->average_rating_for_period->day_votes_count = intval($query->count());
		$this->book->average_rating_for_period->day_rating =
			round($this->book->average_rating_for_period->day_vote_average * $this->book->average_rating_for_period->day_votes_count);
	}

	public function week_rating()
	{
		$query = $this->book->votes()->where('created_at', '>', now()->subWeek());
		$this->book->average_rating_for_period->week_vote_average = floatval($query->avg('vote'));
		$this->book->average_rating_for_period->week_votes_count = intval($query->count());
		$this->book->average_rating_for_period->week_rating =
			round($this->book->average_rating_for_period->week_vote_average * $this->book->average_rating_for_period->week_votes_count);
	}

	public function month_rating()
	{
		$query = $this->book->votes()->where('created_at', '>', now()->subMonth());
		$this->book->average_rating_for_period->month_vote_average = floatval($query->avg('vote'));
		$this->book->average_rating_for_period->month_votes_count = intval($query->count());
		$this->book->average_rating_for_period->month_rating =
			round($this->book->average_rating_for_period->month_vote_average * $this->book->average_rating_for_period->month_votes_count);
	}

	public function quarter_rating()
	{
		$query = $this->book->votes()->where('created_at', '>', now()->subQuarter());
		$this->book->average_rating_for_period->quarter_vote_average = floatval($query->avg('vote'));
		$this->book->average_rating_for_period->quarter_votes_count = intval($query->count());
		$this->book->average_rating_for_period->quarter_rating =
			round($this->book->average_rating_for_period->quarter_vote_average * $this->book->average_rating_for_period->quarter_votes_count);
	}

	public function year_rating()
	{
		$query = $this->book->votes()->where('created_at', '>', now()->subYear());
		$this->book->average_rating_for_period->year_vote_average = floatval($query->avg('vote'));
		$this->book->average_rating_for_period->year_votes_count = intval($query->count());
		$this->book->average_rating_for_period->year_rating =
			round($this->book->average_rating_for_period->year_vote_average * $this->book->average_rating_for_period->year_votes_count);
	}

	public function all_rating()
	{
		$this->book->average_rating_for_period->all_rating = $this->book->vote_average * $this->book->user_vote_count;
	}

	public function copyRating(Book $mainBook, Book $book)
	{
		$book->vote_average = $mainBook->vote_average;
		$book->user_vote_count = $mainBook->user_vote_count;
		$book->in_rating = $mainBook->in_rating;
		$book->rate_info = $mainBook->rate_info;

		$book->male_vote_count = intval($mainBook->male_vote_count);
		$book->female_vote_count = intval($mainBook->female_vote_count);
		$book->male_vote_percent = floatval($mainBook->male_vote_percent);

		$book->average_rating_for_period->day_vote_average = floatval($mainBook->average_rating_for_period->day_vote_average);
		$book->average_rating_for_period->day_votes_count = intval($mainBook->average_rating_for_period->day_votes_count);
		$book->average_rating_for_period->day_rating = intval($mainBook->average_rating_for_period->day_rating);

		$book->average_rating_for_period->week_vote_average = floatval($mainBook->average_rating_for_period->week_vote_average);
		$book->average_rating_for_period->week_votes_count = intval($mainBook->average_rating_for_period->week_votes_count);
		$book->average_rating_for_period->week_rating = intval($mainBook->average_rating_for_period->week_rating);

		$book->average_rating_for_period->month_vote_average = floatval($mainBook->average_rating_for_period->month_vote_average);
		$book->average_rating_for_period->month_votes_count = intval($mainBook->average_rating_for_period->month_votes_count);
		$book->average_rating_for_period->month_rating = intval($mainBook->average_rating_for_period->month_rating);

		$book->average_rating_for_period->quarter_vote_average = floatval($mainBook->average_rating_for_period->quarter_vote_average);
		$book->average_rating_for_period->quarter_votes_count = intval($mainBook->average_rating_for_period->quarter_votes_count);
		$book->average_rating_for_period->quarter_rating = intval($mainBook->average_rating_for_period->quarter_rating);

		$book->average_rating_for_period->year_vote_average = floatval($mainBook->average_rating_for_period->year_vote_average);
		$book->average_rating_for_period->year_votes_count = intval($mainBook->average_rating_for_period->year_votes_count);
		$book->average_rating_for_period->year_rating = intval($mainBook->average_rating_for_period->year_rating);

		$book->push();
	}
}
