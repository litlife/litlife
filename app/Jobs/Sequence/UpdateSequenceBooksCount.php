<?php

namespace App\Jobs\Sequence;

use App\Sequence;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateSequenceBooksCount
{
	use Dispatchable;

	protected $sequence;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Sequence $sequence)
	{
		$this->sequence = $sequence;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if ($this->sequence->isPrivate()) {
			$this->sequence->book_count = $this->sequence->books()
				->acceptedOrBelongsToUser($this->sequence->create_user)
				->count();
		} else {
			$this->sequence->book_count = $this->sequence->books()
				->accepted()
				->count();
		}

		$this->sequence->save();
	}
}
