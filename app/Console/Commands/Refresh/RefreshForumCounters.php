<?php

namespace App\Console\Commands\Refresh;

use App\Events\ForumCountOfPostsHasChanged;
use App\Events\TopicCountOfPostsHasChanged;
use App\Forum;
use App\Jobs\Forum\UpdateForumCounters;
use Illuminate\Console\Command;

class RefreshForumCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:forums_counters';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Пересчитаем различные счетчики у форумов';

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
		Forum::orderBy('id')->chunk(1000, function ($items) {
			foreach ($items as $item) {

				echo('forum: ' . $item->id . "\n");

				UpdateForumCounters::dispatch($item);
			}
		});
	}


}
