<?php

namespace App\Listeners;

use App\Events\NumberOfUnsolvedSupportQuestionsHasChanged;
use App\SupportQuestion;

class FlushCachedNumberOfUnsolvedSupportQuestions
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param NumberOfUnsolvedSupportQuestionsHasChanged $event
	 * @return void
	 */
	public function handle(NumberOfUnsolvedSupportQuestionsHasChanged $event)
	{
		SupportQuestion::flushNumberOfUnsolved();
		$event->user->flushNumberOfUnsolved();
	}
}
