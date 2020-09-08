<?php

namespace App\View\Components;

use Carbon\Carbon;

class Time extends Component
{
	public $time;
	public $title;
	public $text;

	/**
	 * Create a new component instance.
	 *
	 * @param $time
	 * @return void
	 */
	public function __construct($time)
	{
		if (is_string($time)) {
			$time = Carbon::parse($time);
		}

		if ($time instanceof Carbon) {
			if (isset(session()->get('geoip')->timezone))
				$this->time = $time->timezone(session()->get('geoip')->timezone);
		}

		if (!empty($this->time)) {
			$this->title = $this->time->diffForHumans() . '. ' . $this->time->formatLocalized('%A');
			$this->text = trim($this->time->formatLocalized('%e %B %Y %H:%M'));
		}
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		if (empty($this->text))
			return <<<'blade'
blade;

		return <<<'blade'
<span data-toggle="tooltip" data-placement="top" style="cursor:pointer" title="{{ $title }}">{{ $text }}</span>
blade;
	}
}
