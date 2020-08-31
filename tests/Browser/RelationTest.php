<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;

class RelationTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testSubscribeAndUnsubscribe()
	{
		$this->browse(function ($first_browser, $second_browser) {

			$first_user = factory(User::class)->create();
			$second_user = factory(User::class)->create();

			$first_browser->resize(1000, 1000)
				->loginAs($first_user);

			$second_browser->resize(1000, 1000)
				->loginAs($second_user);

			// subscribe

			$first_browser->visit(route('profile', $second_user))
				->assertSee(__('common.subscribe'))
				->clickLink(__('common.subscribe'))
				->assertSee(__('common.unsubscribe'));

			$second_browser->visit(route('profile', $first_user))
				->assertSee(__('common.subscribe'))
				->clickLink(__('common.subscribe'))
				->assertSee(__('common.unsubscribe'));

			// unsubscribe

			$first_browser->visit(route('profile', $second_user))
				->assertSee(__('common.unsubscribe'))
				->clickLink(__('common.unsubscribe'))
				->assertSee(__('common.subscribe'));

			$second_browser->visit(route('profile', $first_user))
				->assertSee(__('common.unsubscribe'))
				->clickLink(__('common.unsubscribe'))
				->assertSee(__('common.subscribe'));

			$first_user->forceDelete();
			$second_user->forceDelete();
		});
	}

	public function testBlockAndUnblock()
	{
		$this->browse(function ($first_browser) {

			$first_user = factory(User::class)->create();
			$second_user = factory(User::class)->create();

			$first_browser->resize(1000, 1000)
				->loginAs($first_user);

			$first_browser->visit(route('profile', $first_user))
				->click('main .dropdown')
				->assertDontSee(mb_strtolower(__('user.add_to_blacklist')));

			// block
			$first_browser->visit(route('profile', $second_user))
				->click('main .dropdown')
				->assertSee(mb_strtolower(__('user.add_to_blacklist')))
				->clickLink(__('user.add_to_blacklist'));

			$first_browser->click('main .dropdown')
				->assertDontSee(mb_strtolower(__('user.add_to_blacklist')))
				->assertSee(mb_strtolower(__('user.remove_from_the_blacklist')));

			// unblock

			$first_browser->visit(route('profile', $second_user))
				->click('main .dropdown')
				->assertSee(mb_strtolower(__('user.remove_from_the_blacklist')))
				->clickLink(__('user.remove_from_the_blacklist'));

			$first_browser->click('main .dropdown')
				->assertSee(mb_strtolower(__('user.add_to_blacklist')));

			$first_user->forceDelete();
			$second_user->forceDelete();
		});
	}
}
