<?php

namespace App\Events;

use App\Author;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthorViewed
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $author;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Author $author)
	{
		$this->author = $author;
	}
}
