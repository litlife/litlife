<?php

namespace App\Events;

use App\Book;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookViewed
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $book;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Book $book)
	{
		$this->book = $book;
	}
}
