<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use App\Events\AuthorViewed;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Jobs\Author\UpdateAuthorCommentsCount;
use App\Jobs\Author\UpdateAuthorRating;
use Illuminate\Console\Command;

class RefreshAuthorCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:author_counters {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все счетчики автора';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$author = Author::any()->findOrFail($this->argument('id'));

		UpdateAuthorBooksCount::dispatch($author);
		UpdateAuthorRating::dispatch($author);
		UpdateAuthorCommentsCount::dispatch($author);

		$author->updateLang();

		event(new AuthorViewed($author));

		$author->save();
	}
}
