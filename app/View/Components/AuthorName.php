<?php

namespace App\View\Components;

use App\Author;

class AuthorName extends Component
{
	public $author;
	public $href = true;
	public $itemprop = '';
	public $showOnline = true;
	private $classes = [];
	private $attr = [];

	/**
	 * Create a new component instance.
	 *
	 * @param Author $author
	 * @param bool $href
	 * @param bool $showOnline
	 * @param string $itemprop
	 * @return void
	 */
	public function __construct($author, $href = true, $showOnline = true, $itemprop = '')
	{
		$this->author = $author;
		$this->href = boolval($href);
		$this->itemprop = $itemprop;
		$this->showOnline = $showOnline;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		if (!isset($this->author))
			return __('Author is not found');

		$output = '';

		$this->classes = ['author', 'name'];

		if ($this->showOnline) {
			if ($this->author->isOnline())
				$this->classes[] = 'online';
		}

		if (!empty($this->itemprop)) {
			$this->attr[] = ' itemprop="' . $this->itemprop . '"';
		}

		if ($this->href) {
			$output .= '<a class="' . implode(' ', $this->classes) . '" ' . implode(' ', $this->attr) . ' href="' . route('authors.show', $this->author) . '">';
		}

		if ($this->author->trashed())
			$output .= __('Author deleted');
		else {
			$output .= '';

			$output .= '';
			$output .= $this->author->last_name . ' ' . $this->author->first_name . ' ' . $this->author->middle_name . ' ' . $this->author->nickname;
			$output .= '';
		}

		if ($this->href) {
			$output .= '</a>';
		}

		if (($this->author->lang != 'RU') and (!empty($this->author->lang))) {
			$output .= ' (' . $this->author->lang . ')';
		}

		if ($this->author->isPrivate()) {
			$output .= ' <i class="fas fa-lock" data-toggle="tooltip" data-placement="top"
			   title="' . __('book.private_tooltip') . '"></i>';
		}

		$output = trim($output);

		return $output;
	}
}
