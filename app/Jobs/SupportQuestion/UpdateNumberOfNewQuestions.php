<?php

namespace App\Jobs\SupportQuestion;

use App\SupportQuestion;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateNumberOfNewQuestions
{
	use Dispatchable, SerializesModels;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws
	 */
	public function handle()
	{
		SupportQuestion::flushNumberOfNewQuestions();
	}
}
