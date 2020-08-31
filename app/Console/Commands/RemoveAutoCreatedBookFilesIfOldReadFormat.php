<?php

namespace App\Console\Commands;

use App\Book;
use App\Jobs\Book\UpdateBookFilesCount;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RemoveAutoCreatedBookFilesIfOldReadFormat extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'clear:remove_auto_created_book_files_if_old_read_format {min_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Удаляет файлы книг, которые были автоматически созданы, если книга в старом формате онлайн чтения';

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

		Book::where('online_read_new_format', false)
			->whereHas('files', function (Builder $query) {
				$query->automaticCreation();
			})
			->chunkById(10, function ($items) {
				foreach ($items as $item) {
					$this->item($item);
				}
			});
	}

	public function item(Book $book)
	{
		UpdateBookFilesCount::dispatch($book);

		$book->refresh();

		if ($book->files_count < 1)
			return false;

		if ($book->isPagesNewFormat())
			return false;

		$files = $book->files()
			->where('auto_created', true)
			->get();

		foreach ($files as $file) {
			$this->comment('Удаляем файл книги ' . $book->id . ' ' . $file->dirname . '/' . $file->name);
			$file->delete();
		}
	}
}
