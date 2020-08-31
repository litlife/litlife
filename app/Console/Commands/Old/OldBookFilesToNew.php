<?php

namespace App\Console\Commands\Old;

use App\BookFile;
use Illuminate\Console\Command;

class OldBookFilesToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:book_files {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$limit = $this->argument('limit');

		$count = BookFile::any()->count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$books = BookFile::any()->take($limit)
				->skip($skip)
				->orderBy('id', 'desc')
				->get();

			foreach ($books as $book) {

				echo($book->id . "\n");

				$this->item($book);
			}
		}
	}

	public function item($book_file)
	{
		$book_file->dirname = $this->getOldBookPath($book_file->book_id);
		$book_file->save();
	}

	function getOldBookPath($BookId)
	{
		$FolderName1 = (floor($BookId / 1000000) * 1000000);
		$FolderName2 = (floor($BookId / 1000) * 1000);
		return 'Book/' . $FolderName1 . '/' . $FolderName2 . '/' . $BookId;
	}
}
