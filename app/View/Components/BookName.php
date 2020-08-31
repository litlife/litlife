<?php

namespace App\View\Components;

use App\User;

class BookName extends Component
{
	public $book;
	public $href = 1;
	public $badge = 1;
	public $showEvenIfTrashed = 0;

	/**
	 * Create a new component instance.
	 *
	 * @param User $book
	 * @param bool $href
	 * @param bool $badge
	 * @param bool $showEvenIfTrashed
	 * @return void
	 */
	public function __construct($book, $href = 1, $badge = 1, $showEvenIfTrashed = 0)
	{
		$this->book = $book;
		$this->href = boolval($href);
		$this->badge = boolval($badge);
		$this->showEvenIfTrashed = boolval($showEvenIfTrashed);
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		$output = '';
		$output .= '<span>';

		if (!isset($this->book))
			$output .= __('Book is not found');
		else {
			if ($this->href) {
				$output .= '<a href="' . route('books.show', $this->book) . '">';
			}

			if ($this->book->trashed() and !$this->showEvenIfTrashed) {
				$output .= __('Book was deleted');
			} elseif (!$this->book->isHaveAccess()) {
				$output .= __('Access to the book is restricted');
			} else {
				$output .= $this->book->title;
			}

			if ($this->href) {
				$output .= '</a>';
			}

			if ($this->badge) {

				if ($this->book->is_collection) {
					$output .= ' <span class="text-muted text-lowercase">(' . __('book.is_collection') . ')</span>';
				}

				if ($this->book->is_si) {
					$output .= ' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="' . __('book.is_si') . '">(' .
						__('book.si') . ')</span>';
				}

				if ($this->book->is_lp) {
					$output .= ' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="' . __('book.is_lp') . '">(' .
						__('book.lp') . ')</span>';
				}

				if ($this->book->age) {
					$output .= ' <sup><span class="text-muted">' . $this->book->age . '+</span></sup>';
				}

				if ($this->book->isPrivate()) {
					$output .= ' <i class="fas fa-lock" data-toggle="tooltip" data-placement="top" title="' . __('book.private_tooltip') . '"></i>';
				}

				if ($this->book->trashed() and $this->showEvenIfTrashed) {
					$output .= ' <span class="text-muted">(' . __('Book was deleted') . ')</span>';
				}

				$output = trim($output);
			}
		}

		$output .= '</span>';

		return $output;
	}
}
