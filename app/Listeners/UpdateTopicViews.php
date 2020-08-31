<?php

namespace App\Listeners;

class UpdateTopicViews
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
	 * @param object $event
	 * @return void
	 */
	public function handle($event)
	{
		$event->topic->view_count++;
		$event->topic->save();
	}
}
