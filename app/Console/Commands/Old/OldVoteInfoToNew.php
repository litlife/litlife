<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldVoteInfoToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:vote_info {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переносит информацию о голосах в новый столбец';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	public function __construct()
	{
		parent::__construct();

		$this->translate_array = array(
			'+5' => '10',
			'+4' => '9',
			'+3' => '8',
			'+2' => '7',
			'+1' => '6',
			'-1' => '5',
			'-2' => '4',
			'-3' => '3',
			'-4' => '2',
			'-5' => '1'
		);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		$count = Book::count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$books = Book::select('id', 'vote_info')
				->take($limit)
				->skip($skip)
				->orderBy('id', 'desc')
				->get();

			foreach ($books as $book) {

				echo($book->id . "\n");

				$this->book($book);
			}
		}
	}

	function book($book)
	{
		$ar = unserialize($book->vote_info);

		if ((isset($book->vote_info)) and (isset($ar)) and (is_array($ar)) and (count($ar) > 0)) {
			$ar_new = [];

			$max = max($ar);

			foreach ($ar as $vote => $count) {
				$new_vote = $this->translate_array[$vote];

				$percent = round((100 * $count) / $max);

				$ar_new[$new_vote] = [
					0 => $count,
					1 => intval(round($percent))
				];
			}

			$str = serialize($ar_new);

			Book::where('id', $book->id)->update(['rate_info' => $str]);
		}
	}
}
