<?php

namespace App\Jobs;

use App\Forum;
use Illuminate\Foundation\Bus\Dispatchable;

class ForumChangePrivate
{
	use Dispatchable;

	protected $forum;
	protected $private;

	/**
	 * Create a new job instance.
	 *
	 * @param Forum $forum
	 * @param bool $private
	 * @return void
	 */
	public function __construct(Forum $forum, $private)
	{
		$this->forum = $forum;

		$this->private = (bool)$private;
	}

	/**
	 * Execute the job.
	 *
	 * @param Forum $forum
	 * @return void
	 */
	public function handle(Forum $forum)
	{
		$this->forum->posts()->any()->update(['private' => $this->private]);
		$this->forum->private = $this->private;
		$this->forum->save();
	}
}
