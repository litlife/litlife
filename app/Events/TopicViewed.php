<?php

namespace App\Events;

use App\Topic;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicViewed
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $topic;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Topic $topic)
	{
		$this->topic = $topic;
	}
}
