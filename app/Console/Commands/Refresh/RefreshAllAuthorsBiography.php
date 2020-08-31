<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshAllAuthorsBiography extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_authors_biography {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все биографии авторов';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Author::any()
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo('author: ' . $item->id . "\n");

					$this->item($item);
				}
			});
	}

	private function item(Author $author)
	{
		DB::transaction(function () use ($author) {

			$biography = $author->biography;

			if (!empty($biography)) {
				$text = $biography->text;
				$biography->text = $text;
				$biography->save();
			}
		});
	}
}
