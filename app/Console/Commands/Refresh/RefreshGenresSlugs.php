<?php

namespace App\Console\Commands\Refresh;

use App\Genre;
use Illuminate\Console\Command;

class RefreshGenresSlugs extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:genre_slugs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет слаги у жанров';

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
		Genre::chunk(100, function ($items) {
			foreach ($items as $item) {
				echo('genre: ' . $item->id . "\n");

				$item->name = trim($item->name);
				$item->save();
			}
		});
	}


}
