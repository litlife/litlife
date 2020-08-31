<?php

namespace App\Console\Commands\Old;

use App\BookSimilarVote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OldSimilarVoteToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:similar_vote_info';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переносит информацию о похожих книгах в новый формат';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		DB::update(DB::raw('UPDATE "book_similar_votes"
SET "book_id" = "book_similars"."book_id", "other_book_id" = "book_similars"."book_id2"
from "book_similars"
WHERE "book_similar_id" = "book_similars"."id"'));

		$limit = 1000;

		$count = BookSimilarVote::count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$book_similar_votes = BookSimilarVote::where("vote", "!=", "0")
				->take($limit)
				->skip($skip)
				->orderBy('created_at', 'asc')
				->get();

			foreach ($book_similar_votes as $book_similar_vote) {

				$this->book_similar_vote($book_similar_vote);
			}
		}

		BookSimilarVote::where('book_id', '=', DB::raw('other_book_id'))->delete();
	}

	function book_similar_vote($book_similar_vote)
	{
		$ar = BookSimilarVote::where("book_id", $book_similar_vote->other_book_id)
			->where("other_book_id", $book_similar_vote->book_id)
			->where("create_user_id", $book_similar_vote->create_user_id)
			->get();

		if (!count($ar)) {
			$book_similar_vote_reverse = new BookSimilarVote;
			$book_similar_vote_reverse->book_id = $book_similar_vote->other_book_id;
			$book_similar_vote_reverse->other_book_id = $book_similar_vote->book_id;
			$book_similar_vote_reverse->vote = $book_similar_vote->vote;
			$book_similar_vote_reverse->create_user_id = $book_similar_vote->create_user_id;
			$book_similar_vote_reverse->book_similar_id = $book_similar_vote->book_similar_id;
			$book_similar_vote_reverse->save();

			$this->info($book_similar_vote_reverse->create_user_id . ' ' . $book_similar_vote_reverse->book_id . ' ' . $book_similar_vote_reverse->other_book_id . ' ' . "\n");
		} else {
			//echo ('- '. $ar->book_id.' '.$ar->other_book_id.' '."\n");
		}


	}

}
