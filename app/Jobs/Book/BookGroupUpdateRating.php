<?php

namespace App\Jobs\Book;

use App\BookGroup;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookGroupUpdateRating
{
	use Dispatchable;

	protected $group;

	/**
	 * Create a new job instance.
	 *
	 * @param BookGroup $group
	 * @return void
	 */
	public function __construct(BookGroup $group)
	{
		$this->group = $group;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			$this->group->vote_average = floatval($this->group->books()->avg('vote_average'));
			$this->group->user_vote_count = intval($this->group->books()->sum('user_vote_count'));

			$this->group->male_vote_count = intval($this->group->books()->sum('male_vote_count'));
			$this->group->female_vote_count = intval($this->group->books()->sum('female_vote_count'));

			$this->group->rate_info = $this->getMergedRateInfo();

			$this->group->save();
		});
	}

	private function getMergedRateInfo()
	{
		$books = $this->group->books()->get();

		$rateInfo = [];

		foreach ($books as $book) {
			foreach ($book->rate_info as $vote => $array) {
				if (empty($rateInfo[$vote]))
					$rateInfo[$vote] = 0;

				$rateInfo[$vote] += $array['count'];
			}
		}

		return $rateInfo;
	}
}
