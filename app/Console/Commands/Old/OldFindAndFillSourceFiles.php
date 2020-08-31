<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldFindAndFillSourceFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:findAndFillSourceFiles {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда ищет среди файлов книг те файлы, которые могли бы быть источниками из которых можно создать страницы онлайн чтения';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		$count = Book::any()->count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$books = Book::any()->select('id')
				->take($limit)
				->skip($skip)
				->with('files')
				->orderBy('id', 'asc')
				->get();

			foreach ($books as $book) {

				echo($book->id . "\n");

				$this->book($book);
			}
		}
	}

	function book($book)
	{
		$files = $book->files;

		$this->findAndMarkSourceFile($files);
	}

	function findAndMarkSourceFile($files)
	{
		$file = $files->where('format', 'fb2')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}

		$file = $files->where('format', 'epub')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}

		$file = $files->where('format', 'rtf')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}

		$file = $files->where('format', 'docx')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}

		$file = $files->where('format', 'doc')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}

		$file = $files->where('format', 'html')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}

		$file = $files->where('format', 'txt')->first();

		if (!empty($file)) {
			$file->source = true;
			$file->save();

			return true;
		}
	}
}
