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

		$this->assertEquals(__('User is not found'), $component->render());
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

		$component = new UserName($user);

		$this->assertEquals('<a href="' . route('profile', $user) . '">' . __('user.deleted') . '</a>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefEnable()
	{
		$user = factory(User::class)->create();

		$component = new UserName($user);

		$this->assertEquals('<a href="' . route('profile', $user) . '"><span style="color: #E14900" class="online">' . $user->userName . '</span></a>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefDisable()
	{
		$user = factory(User::class)->create();

		$component = new UserName($user, false);

		$this->assertEquals('<span style="color: #E14900" class="online">' . $user->userName . '</span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testItemProp()
	{
		$user = factory(User::class)->create();

		$component = new UserName($user, false, 'prop');

		$this->assertEquals('<span style="color: #E14900" class="online" itemprop="prop">' . $user->userName . '</span>',
			$component->render());
	}
}
