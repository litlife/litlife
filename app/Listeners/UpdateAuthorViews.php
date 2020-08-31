<?php

namespace App\Listeners;

class UpdateAuthorViews
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
		if ((empty($event->author->view_updated_at)) or ($event->author->view_updated_at->addMinutes(15)->isPast())) {

			$query = $event->author->any_books()
				->select('books.*', 'view_counts.*')
				->join('view_counts', 'books.id', '=', 'view_counts.book_id');

			$event->author->view_day = (clone $query)->sum('view_counts.day');
			$event->author->view_week = (clone $query)->sum('view_counts.week');
			$event->author->view_month = (clone $query)->sum('view_counts.month');
			$event->author->view_year = (clone $query)->sum('view_counts.year');
			$event->author->view_all = (clone $query)->sum('view_counts.all');
			$event->author->view_updated_at = now();
			$event->author->save();
		}
	}
}
