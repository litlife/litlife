<?php

namespace App\Events;

use App\Section;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnchorsInSectionHasChanged
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $section;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Section $section)
	{
		$this->section = $section;
	}
}
