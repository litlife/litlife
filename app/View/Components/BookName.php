<?php

namespace App\View\Components;

use App\User;

class BookName extends Component
{
	public $book;
	public $href = 1;
	public $badge = 1;
	public $showEvenIfTrashed = 0;

	public $title;
	public $age = false;

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
		$this->href = $href;
		$this->badge = $badge;
		$this->showEvenIfTrashed = boolval($showEvenIfTrashed);

		if ($this->badge === true or $this->badge === 'true')
			$this->badge = 1;

		if (!isset($this->book)) {
			$this->title = __('Book is not found');
			$this->href = false;
			$this->badge = false;
		} else {
			if ($this->href == 1) {
				$this->href = route('books.show', $this->book);
			}

			if ($this->book->trashed() and !$this->showEvenIfTrashed) {
				$this->title = __('Book was deleted');
				$this->badge = false;
			} elseif (!$this->book->isHaveAccess()) {
				$this->title = __('Access to the book is restricted');
				$this->badge = false;
			} else {
				$this->title = $this->book->title;
				$this->age = $this->book->age;
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
		$output = '';
		$output .= '<span>';

		if ($this->href) {
			$output .= '<a href="{{ $href }}">';
		}

		$output .= '{{ $title }}';

		if ($this->href) {
			$output .= '</a>';
		}

		if ($this->badge) {

			if ($this->book->is_collection) {
				$output .= ' <span class="text-muted text-lowercase">({{ __("book.is_collection") }})</span>';
			}

			if ($this->book->is_si) {
				$output .= ' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ __("book.is_si") }}">';
				$output .= '({{ __("book.si") }})';
				$output .= '</span>';
			}

			if ($this->book->is_lp) {
				$output .= ' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ __("book.is_lp") }}">';
				$output .= '({{ __("book.lp") }})';
				$output .= '</span>';
			}

			if ($this->book->age) {
				$output .= ' <sup><span class="text-muted">{{ $age }}+</span></sup>';
			}

			if ($this->book->isPrivate()) {
				$output .= ' <i class="fas fa-lock" data-toggle="tooltip" data-placement="top" title="{{ __("book.private_tooltip") }}"></i>';
			}

			if ($this->book->trashed() and $this->showEvenIfTrashed) {
				$output .= ' <span class="text-muted">{{ __("Book was deleted") }}</span>';
			}

			$output = trim($output);
		}


		$output .= '</span>';

		return $output;
	}
}
