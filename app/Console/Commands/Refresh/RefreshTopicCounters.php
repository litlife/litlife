<?php

namespace App\Console\Commands\Refresh;

use App\Events\TopicCountOfPostsHasChanged;
use App\Jobs\Topic\UpdateTopicCounters;
use App\Topic;
use Illuminate\Console\Command;

class RefreshTopicCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:topics_counters';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Пересчитаем различные счетчики у тем форума';

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
		Topic::orderBy('id')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				echo('topic: ' . $item->id . "\n");

				UpdateTopicCounters::dispatch($item);
			}
		});
	}


}
