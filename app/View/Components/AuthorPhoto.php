<?php

namespace App\View\Components;

use App\Author;
use Litlife\Url\Url;

class AuthorPhoto extends Component
{
	public $author;
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

	/**
	 * Create a new component instance.
	 *
	 * @param Author $author
	 * @param int $width
	 * @param int $height
	 * @param int $quality
	 * @param int $href
	 * @param string $class
	 * @return void
	 */
	public function __construct($author, $width, $height, $quality = 90, $href = 1, $class = '', $style = '')
	{
		$this->author = $author;
		$this->width = $width;
		$this->height = $height;

		if (empty($this->author)) {
			$href = 0;
		}

		if ($href) {
			if ($href == 1) {
				$this->href = route('authors.show', $author);
			} else {
				$this->href = $href;
			}
		} else
			$this->href = 0;


		if ($this->isShowPhoto()) {
			$this->url = Url::fromString($this->author->photo->url);
		} else {
			if (!empty($this->author)) {
				if ($this->author->gender == 'female') {
					$this->url = Url::fromString(mix('images/no_image_female.png', 'assets'));
				} elseif ($this->author->gender == 'male') {
					$this->url = Url::fromString(mix('images/no_image_male.png', 'assets'));
				} else {
					$this->url = Url::fromString(mix('images/no_image_unknown.png', 'assets'));
				}
			} else {
				$this->url = Url::fromString(mix('images/no_image_unknown.png', 'assets'));
			}
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

		if (empty($this->author) or $this->author->trashed() or !$this->author->isHaveAccess()) {
			$this->alt = null;
		} else {
			$this->alt = htmlspecialchars($this->author->name);
		}

		$array = explode(' ', $class);
		$array = array_merge($array, ['lazyload', 'img-fluid', 'rounded', 'text-center']);
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

		if (!empty($this->author)) {
			if ($this->href) {
				$output .= <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto">
blade;
			}
		}

		$output .= <<<'blade'
<img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/>
blade;

		if (!empty($this->author)) {
			if ($this->href) {
				$output .= <<<'blade'
</a>
blade;
			}
		}

		return $output;
	}

	public function isShowPhoto(): bool
	{
		if (empty($this->author))
			return false;

		if ($this->author->trashed())
			return false;

		if (empty($this->author->photo))
			return false;

		if ($this->author->photo->trashed())
			return false;

		if (!$this->author->isHaveAccess())
			return false;

		return true;
	}
}
