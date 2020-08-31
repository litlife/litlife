<?php

namespace App\View\Components;

use App\User;
use Litlife\Url\Url;

class UserAvatar extends Component
{
	public $user;
	public $href = 1;
	public $quality;
	public $alt;
	public $noImageAvailable;
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
	 * @param User $user
	 * @param int $width
	 * @param int $height
	 * @param int $quality
	 * @param int $href
	 * @param string $class
	 * @return void
	 */
	public function __construct($user, $width, $height, $quality = 90, $href = 1, $class = '', $style = '')
	{
		$this->user = $user;
		$this->width = $width;
		$this->height = $height;

		if (empty($this->user)) {
			$href = 0;
		}

		if ($href) {
			if ($href == 1) {
				$this->href = route('profile', $user);
			} else {
				$this->href = $href;
			}
		} else
			$this->href = 0;

		if (empty($this->user) or $this->user->trashed()) {
			$this->url = Url::fromString(mix('images/no_image_male.png', 'assets'));

			$this->noImageAvailable = true;
		} elseif (empty($this->user->avatar)) {

			if ($this->user->gender == 'female') {
				$this->url = Url::fromString(mix('images/no_image_female.png', 'assets'));
			} elseif ($this->user->gender == 'male') {
				$this->url = Url::fromString(mix('images/no_image_male.png', 'assets'));
			} else {
				$this->url = Url::fromString(mix('images/no_image_unknown.png', 'assets'));
			}

			$this->noImageAvailable = true;

		} else {
			$this->url = Url::fromString($this->user->avatar->url);
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

		if (empty($this->user) or $this->user->trashed()) {
			$this->alt = null;
		} else {
			$this->alt = htmlspecialchars($this->user->userName);
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

		if (!empty($this->user)) {
			if ($this->href) {
				$output .= <<<'blade'
<a title="{{ $user->userName }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto">
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

		if (!empty($this->user)) {
			if ($this->href) {
				$output .= <<<'blade'
</a>
blade;
			}
		}

		return $output;
	}
}
