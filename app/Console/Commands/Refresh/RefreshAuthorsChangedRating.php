<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use App\Events\BookFilesCountChanged;
use App\Jobs\Author\UpdateAuthorRating;
use Illuminate\Console\Command;

class RefreshAuthorsChangedRating extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:authors_changed_rating';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update authors raiting that are changed';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Author::any()->where("rating_changed", true)
			->chunkById(100, function ($authors) {
				foreach ($authors as $author) {
					UpdateAuthorRating::dispatch($author);
				}
			});
	}
}
