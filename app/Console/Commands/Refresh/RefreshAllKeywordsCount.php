<?php

namespace App\Console\Commands\Refresh;


use App\Keyword;
use Illuminate\Console\Command;

class RefreshAllKeywordsCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:keywords';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Пересчитаем количество использований у всех ключевых слов';

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
		Keyword::any()
			->orderBy('id', 'asc')
			->chunk(100, function ($items) {
				foreach ($items as $item) {
					echo('keyword: ' . $item->id . "\n");

					$item->updateBooksCount();
					$item->save();
				}
			});
	}


}
