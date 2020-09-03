<?php

namespace App\Jobs\Notification;

use App\Notifications\BookFinishParseNotification;
use Illuminate\Foundation\Bus\Dispatchable;

class BookFinishParseJob
{
	use Dispatchable;

	protected $book_parse;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($book_parse)
	{
		$this->book_parse = $book_parse;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$notifiable = $this->book_parse->create_user;

		if (!empty($notifiable)) {
			if (!empty($this->book_parse->book) and !$this->book_parse->book->trashed()) {

				if ($this->book_parse->isParseOnlyPages()) {
					$notifiable->notify(new BookFinishParseNotification($this->book_parse));
				}
			}
		}
	}
}
