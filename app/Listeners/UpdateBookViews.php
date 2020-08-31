<?php

namespace App\Listeners;

use App\BookViewIp;
use App\Events\BookViewed;
use App\ViewCount;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class UpdateBookViews
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
	 * @param BookViewed $event
	 * @return void
	 */
	public function handle(BookViewed $event)
	{
		DB::transaction(function () use ($event) {

			$view = $event->book->user_view_ips()->where('ip', request()->ip());

			if (!$view->first()) {

				$view = new BookViewIp;
				$view->book_id = $event->book->id;
				$view->ip = request()->ip();
				$view->save();

				$view_count = $event->book->view_count;

				if (empty($view_count))
					$view_count = new ViewCount;

				$view_count->day++;
				$view_count->week++;
				$view_count->month++;
				$view_count->year++;
				$view_count->all++;

				try {
					$event->book->view_count()->save($view_count);
				} catch (QueryException $exception) {

				}
			}
		});
	}
}
