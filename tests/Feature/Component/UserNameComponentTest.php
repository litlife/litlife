<?php

namespace Tests\Feature\Component;

use App\User;
use App\View\Components\UserName;
use Tests\TestCase;

class UserNameComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testDeleted()
	{
		$user = null;

		$component = new UserName($user, true, '');
		/*
				$this->assertEquals(__('User is not found'), $component->render());
		*/
		$this->assertEquals('{{ $name }}', $component->render());

		$this->assertEquals(__('User is not found'), $component->name);
		$this->assertEquals('', $component->itemprop);
		$this->assertEquals(false, $component->href);
		$this->assertEquals('', $component->class);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSoftDeleted()
	{
		$user = User::factory()->create();
		$user->delete();

		$component = new UserName($user);
		/*
				$this->assertEquals('<a href="' . route('profile', $user) . '">' . __('user.deleted') . '</a>',
					$component->render());
				*/

		$this->assertEquals('<a href="{{ $href }}"><span style="color: #E14900" class="{{ $class }}">{{ $name }}</span></a>', $component->render());

		$this->assertEquals(__('user.deleted'), $component->name);
		$this->assertEquals('', $component->itemprop);
		$this->assertEquals(route('profile', $user), $component->href);
		$this->assertEquals('', $component->class);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefEnable()
	{
		$user = User::factory()->create();

		$component = new UserName($user);
		/*
				$this->assertEquals('<a href="' . route('profile', $user) . '"><span style="color: #E14900" class="online">' . $user->userName . '</span></a>',
					$component->render());
		*/
		$this->assertEquals('<a href="{{ $href }}"><span style="color: #E14900" class="{{ $class }}">{{ $name }}</span></a>', $component->render());

		$this->assertEquals($user->userName, $component->name);
		$this->assertEquals('', $component->itemprop);
		$this->assertEquals(route('profile', $user), $component->href);
		$this->assertEquals('online', $component->class);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefDisable()
	{
		$user = User::factory()->create();

		$component = new UserName($user, false);
		/*
				$this->assertEquals('<span style="color: #E14900" class="online">' . $user->userName . '</span>',
					$component->render());
				*/
		$this->assertEquals('<span style="color: #E14900" class="{{ $class }}">{{ $name }}</span>', $component->render());

		$this->assertEquals($user->userName, $component->name);
		$this->assertEquals('', $component->itemprop);
		$this->assertEquals(false, $component->href);
		$this->assertEquals('online', $component->class);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testItemProp()
	{
		$user = User::factory()->create();

		$component = new UserName($user, false, 'prop');
		/*
				$this->assertEquals('<span style="color: #E14900" class="online" itemprop="prop">' . $user->userName . '</span>',
					$component->render());
		*/
		$this->assertEquals('<span style="color: #E14900" class="{{ $class }}" itemprop="{{ $itemprop }}">{{ $name }}</span>',
			$component->render());

		$this->assertEquals($user->userName, $component->name);
		$this->assertEquals('prop', $component->itemprop);
		$this->assertEquals(false, $component->href);
		$this->assertEquals('online', $component->class);
	}
}
