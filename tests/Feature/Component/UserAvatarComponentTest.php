<?php

namespace Tests\Feature\Component;

use App\User;
use App\View\Components\UserAvatar;
use Tests\TestCase;

class UserAvatarComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testDeleted()
	{
		$user = null;

		$component = new UserAvatar($user, 200, 200);

		$expected = <<<'blade'
<img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/>
blade;

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(null, $data['alt']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSoftDeleted()
	{
		$user = factory(User::class)->create();
		$user->delete();

		$component = new UserAvatar($user, 200, 200);

		$expected = <<<'blade'
<a title="{{ $user->userName }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/></a>
blade;

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(null, $data['alt']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefEnable()
	{
		$user = factory(User::class)->create();

		$component = new UserAvatar($user, 200, 200, 90, 1);

		$expected = <<<'blade'
<a title="{{ $user->userName }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/></a>
blade;

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals($user->userName, $data['alt']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefDisable()
	{
		$user = factory(User::class)->create();

		$component = new UserAvatar($user, 200, 200, 90, 0);

		$expected = <<<'blade'
<img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/>
blade;

		$this->assertEquals($expected, $component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testMaxWidthOverwrite()
	{
		$user = factory(User::class)->create();

		$component = new UserAvatar($user, 200, 200, 90, 0, '', 'max-width:100%');

		$this->assertEquals('max-width: 100%; max-height: 200px;', $component->data()['style']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testMaxWidthMaxHeight()
	{
		$user = factory(User::class)->create();

		$component = new UserAvatar($user, 200, 200, 90, 0, '');

		$this->assertEquals('max-width: 200px; max-height: 200px;', $component->data()['style']);
	}
}
