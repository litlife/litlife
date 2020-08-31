<?php

namespace App\View\Components;

use App\Book;
use Litlife\Url\Url;

class BookCover extends Component
{
	public $book;
	public $href = 1;
	public $quality;
	public $alt;
	public $width;
	public $height;
	public $url;
	public $url2x;
	public $url3x;
	public $class;
	public $data;
	public $style;
	public $showEvenIfTrashed = 0;

	/**
	 * Create a new component instance.
	 *
	 * @param Book $book
	 * @param int $width
	 * @param int $height
	 * @param int $quality
	 * @param int $href
	 * @param string $class
	 * @return void
	 */
	public function __construct($book, $width, $height, $quality = 90, $href = 1, $class = '', $style = '', $showEvenIfTrashed = 0)
	{
		$this->book = $book;
		$this->width = $width;
		$this->height = $height;
		$this->showEvenIfTrashed = $showEvenIfTrashed;

		if (empty($this->book))
			$href = 0;

		if ($href) {
			if ($href == 1) {
				$this->href = route('books.show', $book);
			} else {
				$this->href = $href;
			}
		} else
			$this->href = 0;

		if ($this->isShowCover()) {
			$this->url = Url::fromString($this->book->cover->url);
		} else {
			$this->url = Url::fromString(mix('images/no_book_cover.jpeg', 'assets'));
		}

		$this->url = $this->url
			->withQueryParameter('w', $width)
			->withQueryParameter('h', $height)
			->withQueryParameter('q', $quality);

		$this->url2x = (string)$this->url
			->withQueryParameter('w', $this->width * 2)
			->withQueryParameter('h', $this->height * 2)
			->withQueryParameter('q', $quality - 5);

		$this->url3x = (string)$this->url
			->withQueryParameter('w', $this->width * 3)
			->withQueryParameter('h', $this->height * 3)
			->withQueryParameter('q', $quality - 10);

		$this->url = (string)$this->url;

		if (empty($this->book) or $book->trashed() or !$book->isHaveAccess()) {
			$this->alt = null;
		} else {
			$this->alt = htmlspecialchars($this->book->title);
		}

		$array = explode(' ', $class);
		$array = array_merge($array, ['lazyload', 'rounded']);
		$this->class = implode(' ', $array);

		$array = [
			/*
			'margin-left' => 'auto',
            'margin-right' => 'auto'
			*/
		];

		foreach (explode(';', $style) as $prop) {
			if (str_contains($prop, ':')) {
				list($key, $value) = explode(':', $prop);

				$array[$key] = $value;
			}
		}

		if (!array_key_exists('max-width', $array))
			$array['max-width'] = $this->width . 'px';

		if (!array_key_exists('max-height', $array))
			$array['max-height'] = $this->height . 'px';

		$style = '';

		foreach ($array as $key => $value) {
			$style .= $key . ': ' . $value . '; ';
		}

		$this->style = trim($style);
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		$output = '';

		if (!empty($this->book)) {
			if ($this->href) {
				$output .= <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto">
blade;
			}
		}

		$output .= <<<'blade'
<img class="{{ $class }}" alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}" data-src="{{ $url }}"/>
blade;

		if (!empty($this->book)) {
			if ($this->href) {
				$output .= <<<'blade'
</a>
blade;
			}
		}

		return $output;
	}

	public function isShowCover(): bool
	{
		if (empty($this->book))
			return false;

		if ($this->book->trashed()) {
			if (!$this->showEvenIfTrashed)
				return false;
		}

		if (empty($this->book->cover))
			return false;

		if ($this->book->cover->trashed())
			return false;

		if (!$this->book->isHaveAccess())
			return false;

		return true;
	}
}
