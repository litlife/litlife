<?php

namespace App\Jobs\Forum;

use App\Forum;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateForumCounters
{
	use Dispatchable;

	protected $forum;

	/**
	 * Create a new job instance.
	 *
	 * @param Forum $forum
	 * @return void
	 */
	public function __construct(Forum $forum)
	{
		$this->forum = $forum;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->forum->topicsCountRefresh();
		$this->forum->postsCountRefresh();
		$this->forum->lastPostRefresh();
		$this->forum->save();
	}
}
