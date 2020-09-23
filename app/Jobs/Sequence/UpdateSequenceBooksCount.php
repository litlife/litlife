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
	 * @param Sequence $sequence
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
		$this->sequence->updateBooksCount();
		$this->sequence->save();
	}
}
