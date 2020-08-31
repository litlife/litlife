<?php

namespace App\View\Components;

use App\User;

class UserName extends Component
{
	public $user;
	public $href = true;
	public $itemprop = '';

	/**
	 * Create a new component instance.
	 *
	 * @param User $user
	 * @return void
	 */
	public function __construct($user, $href = true, $itemprop = '')
	{
		$this->user = $user;
		$this->href = boolval($href);
		$this->itemprop = $itemprop;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		if (!isset($this->user))
			return __('User is not found');

		$output = '';

		if ($this->href) {
			$output .= '<a href="' . route('profile', $this->user) . '">';
		}

		if ($this->user->trashed())
			$output .= __('user.deleted');
		else {
			$output .= '<span style="color: #E14900"';

			if ($this->user->isOnline()) {
				$output .= ' class="online"';
			}

			if (!empty($this->itemprop)) {
				$output .= ' itemprop="' . $this->itemprop . '"';
			}

			$output .= '>';
			$output .= $this->user->userName;
			$output .= '</span>';
		}

		if ($this->href) {
			$output .= '</a>';
		}

		return $output;
	}
}
