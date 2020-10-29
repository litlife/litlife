<?php

namespace App\Listeners;

use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\SupportRequest;

class FlushCachedNumberOfUnsolvedSupportRequests
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
	 * @param NumberOfUnsolvedSupportRequestsHasChanged $event
	 * @return void
	 */
	public function handle(NumberOfUnsolvedSupportRequestsHasChanged $event)
	{
		SupportRequest::flushNumberOfUnsolved();
		$event->user->flushNumberOfUnsolved();
	}
}
