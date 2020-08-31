<?php

namespace App\Console\Commands\BookFile;

use App\Book;
use Illuminate\Console\Command;

class CreateNewFilesForAllBooks extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bookfiles:create_all {limit=10} {id_more_than=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Создать новые файлы книг для всех книг которые представлены в новом формате';

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
		$id_more_than = $this->argument('id_more_than');

		Book::anyNotTrashed()
			->where('id', '>', $id_more_than)
			->onlineReadNewFormat()
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->call('bookfiles:make', [
						'bookId' => $item->id
					]);
				}
			});

	}
}
