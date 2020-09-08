<?php

namespace App\View\Components;

use App\Author;

class AuthorName extends Component
{
	public $author;
	public $href = 1;
	public $itemprop = '';
	private $classes = [];

	public $name;
	public $lang;
	public $class;

	/**
	 * Create a new component instance.
	 *
	 * @param Author $author
	 * @param bool $href
	 * @param bool $showOnline
	 * @param string $itemprop
	 * @return void
	 */
	public function __construct($author, $href = 1, $showOnline = true, $itemprop = '')
	{
		$this->author = $author;
		$this->href = $href;
		$this->itemprop = $itemprop;

		if ($this->href === true or $this->href === 'true')
			$this->href = 1;

		if (!isset($this->author)) {
			$this->name = __('Author is not found');
			$this->href = false;
		} else {

			if ($this->href == 1) {
				$this->href = route('authors.show', $author);
			}

			if ($this->author->trashed()) {
				$this->name = __('Author deleted');
			} else {
				$this->name = trim($this->author->last_name . ' ' . $this->author->first_name . ' ' . $this->author->middle_name . ' ' . $this->author->nickname);
				$this->lang = $this->author->lang;

				if ($showOnline) {
					if ($this->author->isOnline())
						array_push($this->classes, 'online');
				}
			}

			array_push($this->classes, 'author');
			array_push($this->classes, 'name');

			$this->class = implode(' ', $this->classes);
		}
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		if (!isset($this->author))
			return '{{ $name }}';

		$output = '';

		if ($this->href) {
			$output .= '<a class="{{ $class }}" href="{{ $href }}">';
		}

		$output .= '{{ $name }}';

		if ($this->href) {
			$output .= '</a>';
		}

		if (($this->lang != 'RU') and (!empty($this->lang))) {
			$output .= ' ({{ $lang }})';
		}

		if ($this->author->isPrivate()) {
			$output .= ' <i class="fas fa-lock" data-toggle="tooltip" data-placement="top"
			   title="{{ __("book.private_tooltip") }}"></i>';
		}

		$output = trim($output);

		return $output;
	}
}
