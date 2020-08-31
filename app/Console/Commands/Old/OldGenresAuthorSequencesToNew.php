<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldGenresAuthorSequencesToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:genres_authors_sequences_to_new ';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переводит данные из жанров, авторов, переводчиков, серий в новый формат';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()->orderBy('id')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				$this->book($item);
			}
		});
	}

	function book($book)
	{
		echo($book->id . "\n");

		$this->writers($book);
		$this->translators($book);
		$this->sequences($book);
		$this->genres($book);
	}

	function translators($book)
	{
		$ids = explode(",", $book->transltor);

		$ids = array_unique($ids);

		foreach ($ids as $order => $id) {
			if ($id) {
				$sync_array[$id] = [
					'order' => $order
				];
			}
		}

		if (!empty($sync_array))
			$book->translators()->syncWithoutDetaching($sync_array);
	}

	function sequences($book)
	{
		$series = explode(",", $book->series);
		$nis = explode(",", $book->nis);

		foreach ($series as $order => $sequence_id) {

			$sequence_id = intval($sequence_id);

			if ($sequence_id) {
				$number = intval($nis[$order]);

				if (!$number)
					$number = null;

				if ($sequence_id)
					$sync_array[$sequence_id] = [
						'order' => $order,
						'number' => $number
					];
			}
		}

		if (!empty($sync_array))
			$book->sequences()->syncWithoutDetaching($sync_array);
	}

	function genres($book)
	{
		$ids = explode(",", $book->genre);

		$ids = array_unique($ids);

		foreach ($ids as $order => $id) {
			if ($id) {
				$sync_array[$id] = [
					'order' => $order
				];
			}
		}

		if (!empty($sync_array))
			$book->genres()->syncWithoutDetaching($sync_array);
	}

	function authors($book)
	{
		$ids = explode(",", $book->author);

		$ids = array_unique($ids);

		foreach ($ids as $order => $id) {
			if ($id) {
				$sync_array[$id] = [
					'order' => $order
				];
			}
		}

		if (!empty($sync_array))
			$book->writers()->syncWithoutDetaching($sync_array);
	}
}
