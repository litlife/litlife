<?php

namespace App\Console\Commands;

use App\Book;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use Illuminate\Console\Command;

class RemoveAutoCreatedBookFilesIfNoChaptersExists extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'clear:remove_auto_created_book_files_if_no_chapters_exists {min_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Удаляет epub файлы книг, которые были автоматически созданы, если у книги отсутствуют главы и количество символов в книге пустое';

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
		$min_id = $this->argument('min_id');

		Book::where('id', '>=', $min_id)
			->where(function ($query) {
				$query->where('characters_count', '<', 1)
					->orWhereNull('characters_count');
			})
			->where(function ($query) {
				$query->where('sections_count', '<', 1)
					->orWhereNull('sections_count');
			})
			->where('files_count', '>', 0)
			->onlineReadNewFormat()
			->chunkById(10, function ($items) {
				foreach ($items as $item) {
					$this->item($item);
				}
			});
	}

	public function item(Book $book)
	{
		$book->refreshCharactersCount();
		UpdateBookFilesCount::dispatch($book);
		UpdateBookSectionsCount::dispatch($book);

		$book->refresh();

		if ($book->files_count < 1)
			return false;

		if ($book->characters_count > 0)
			return false;

		if ($book->sections_count > 0)
			return false;

		if (!$book->isPagesNewFormat())
			return false;

		$files = $book->files()
			->where('format', 'epub')
			->get();

		foreach ($files as $file) {
			$this->comment('Удаляем файл книги ' . $book->id . ' ' . $file->dirname . '/' . $file->name);
			$file->delete();
		}
	}
}
