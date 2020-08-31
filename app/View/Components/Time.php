<?php

namespace App\View\Components;

use Carbon\Carbon;

class Time extends Component
{
	public $time;

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
			$this->time = $time->timezone(session()->get('geoip')->timezone);
		}
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		if (empty($this->time))
			return '';

		$output = '<span data-toggle="tooltip" data-placement="top" style="cursor:pointer" title="' .
			$this->time->diffForHumans() . '. ' . $this->time->formatLocalized('%A') . '">';
		$output .= trim($this->time->formatLocalized('%e %B %Y %H:%M'));
		$output .= '</span>';

		return $output;
	}
}
