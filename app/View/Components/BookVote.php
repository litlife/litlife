<?php

namespace App\View\Components;

class BookVote extends Component
{
	public $vote;

	/**
	 * Create a new component instance.
	 *
	 * @param $vote
	 * @return void
	 */
	public function __construct($vote)
	{
		if ($vote instanceof \App\BookVote) {
			$this->vote = $vote->vote;
		} else {
			$this->vote = $vote;
		}
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		$output = '';

		if ($this->vote >= 7)
			$output .= '<span class="badge badge-success">' . $this->vote . '</span>';
		elseif ($this->vote <= 3)
			$output .= '<span class="badge badge-danger">' . $this->vote . '</span>';
		else
			$output .= '<span class="badge badge-secondary">' . $this->vote . '</span>';

		return $output;
	}
}
