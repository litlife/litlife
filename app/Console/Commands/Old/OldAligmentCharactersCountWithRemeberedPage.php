<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldAligmentCharactersCountWithRemeberedPage extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:aligment_characters_count_with_remembered_page {limit=1000} {start_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество символов для всех запомненных страниц книг к количество символов в книге';

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
		Book::any()
			->where('id', '>=', $this->argument('start_id'))
			->chunkById(1000, function ($items) {
				foreach ($items as $item) {
					$this->item($item);
				}
			});
	}

	public function item($item)
	{
		if ($item->characters_count > 0) {
			$item->remembered_pages()
				->update(['characters_count' => $item->characters_count]);
		}
	}
}
