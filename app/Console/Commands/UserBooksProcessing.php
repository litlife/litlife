<?php

namespace App\Console\Commands;

use App\Book;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class UserBooksProcessing extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:books_processing {user_id}
                                 {--publish=true}
                                 {--delete_files=true}
                                 {--disable_download_access=true}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обрабатывает книги пользователя';
	private $user;
	private $bar;

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
		$this->user = User::findOrFail($this->argument('user_id'));

		$query = $this->user
			->created_books()
			->whereHas('parse', function (Builder $query) {
				$query->succeedParse();
			});

		$this->bar = $this->output->createProgressBar($query->count());
		$this->bar->setFormatDefinition('custom', ' %current%/%max% -- %message%');
		$this->bar->setFormat('custom');

		$query->chunkById(10, function ($books) {
				foreach ($books as $book) {
					$this->book($book);
					$this->bar->advance();
				}
			});

		$this->bar->finish();
	}

	protected function book(Book $book)
	{
		if ($this->option('publish')) {
			$this->publish($book);
		}

		if ($this->option('delete_files')) {
			$this->deleteFiles($book);
		}

		if ($this->option('disable_download_access')) {
			$this->disableDownloadAccess($book);
		}

		$book->save();
	}

	protected function publish(Book $book)
	{
		$book->publish();
	}

	protected function deleteFiles(Book $book)
	{
		foreach ($book->files as $file) {
			Storage::disk($file->storage)
				->delete($file->dirname . '/' . $file->name);
		}
	}

	protected function disableDownloadAccess(Book $book)
	{
		$book->downloadAccessDisable();
	}
}