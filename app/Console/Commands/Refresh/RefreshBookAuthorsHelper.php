<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use Illuminate\Console\Command;

class RefreshBookAuthorsHelper extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:authors_name_helper {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет строку с полным именем автора';

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
		Author::any()->chunkById($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	public function item(Author $item)
	{
		echo("Author $item->id \n");

		$item->updateNameHelper();
		$item->save();
	}
}
