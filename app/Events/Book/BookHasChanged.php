<?php

namespace App\Events\Book;

use App\Book;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookHasChanged
{
	use Dispatchable, SerializesModels;

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

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		//return new PrivateChannel('channel-name');
	}
}
