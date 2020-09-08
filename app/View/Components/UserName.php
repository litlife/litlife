<?php

namespace App\View\Components;

use App\User;

class UserName extends Component
{
	public $user;
	public $href = false;
	public $itemprop = '';
	public $name;
	public $class = '';

	/**
	 * Create a new component instance.
	 *
	 * @param User $user
	 * @return void
	 */
	public function __construct($user, $href = true, $itemprop = '')
	{
		$this->user = $user;
		$this->itemprop = $itemprop;

		if (!isset($this->user)) {
			$this->name = __('User is not found');
			$this->href = false;
		} else {
			if ($this->user->trashed()) {
				$this->name = __('user.deleted');
			} else {
				$this->name = $this->user->userName;

				if (!empty($this->itemprop))
					$this->itemprop = $itemprop;

				if ($this->user->isOnline()) {
					$this->class = 'online';
				}
			}

			if ($href) {
				$this->href = route('profile', $this->user);
			}
		}
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		if (!isset($this->user))
			return '{{ $name }}';

		$output = '';

		if ($this->href) {
			$output .= '<a href="{{ $href }}">';
		}

		$output .= '<span style="color: #E14900"';
		$output .= ' class="{{ $class }}"';

		if (!empty($this->itemprop)) {
			$output .= ' itemprop="{{ $itemprop }}"';
		}

		$output .= '>';
		$output .= '{{ $name }}';
		$output .= '</span>';

		if ($this->href) {
			$output .= '</a>';
		}

		return $output;
	}
}
