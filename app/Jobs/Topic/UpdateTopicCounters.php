<?php

namespace App\Jobs\Topic;

use App\Topic;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateTopicCounters
{
	use Dispatchable;

	protected $topic;

	/**
	 * Create a new job instance.
	 *
	 * @param Topic $topic
	 * @return void
	 */
	public function __construct(Topic $topic)
	{
		$this->topic = $topic;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->topic->postsCountRefresh();
		$this->topic->lastPostRefresh();
		$this->topic->save();
	}
}
