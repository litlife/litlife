<?php

namespace Tests\Browser;

use App\User;
use App\UserEmail;
use Tests\DuskTestCase;

class UserEmailTest extends DuskTestCase
{
	public function testShowHideInProfile()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->states('with_confirmed_email')
				->create();

			$email = $user->emails()->get()->first();
			$email->show_in_profile = false;
			$email->save();
			//$second_email = factory(UserEmail::class)->create(['user_id' => $user->id]);

			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.emails.index', $user))
				->assertSee($email->email);

			// show in profile

			$browser->with('.email[data-id="' . $email->id . '"]', function ($email) {
				$email->click('.dropdown-toggle')
					->with('.dropdown-menu', function ($menu) {
						$menu->assertSee(__('user_email.show_in_profile_enable'))
							->clickLink(__('user_email.show_in_profile_enable'));
					});
			})->assertSee(__('user_email.now_showed_in_profile', ['email' => $email->email]));

			$this->assertTrue($email->fresh()->show_in_profile);

			$browser->visit(route('profile', $user))
				->assertSee($email->email);

			// disable show in profile

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $email->id . '"]', function ($email) {
					$email->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($menu) {
							$menu->assertSee(__('user_email.show_in_profile_disable'))
								->clickLink(__('user_email.show_in_profile_disable'));
						});
				})->assertSee(__('user_email.now_not_showed_in_profile', ['email' => $email->email]));

			$this->assertFalse($email->fresh()->show_in_profile);

			$browser->visit(route('profile', $user))
				->assertDontSee($email->email);
		});
	}

	public function testRescueEnableAndDisable()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->states('with_confirmed_email')
				->create();

			$email = $user->emails()->get()->first();
			$email->rescue = true;
			$email->save();
			//$second_email = factory(UserEmail::class)->create(['user_id' => $user->id]);

			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.emails.index', $user))
				->assertSee($email->email);

			// try disable rescue

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $email->id . '"]', function ($email) {
					$email->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($menu) {
							$menu->assertSee(__('user_email.rescue_disable'))
								->clickLink(__('user_email.rescue_disable'));
						});
				})->assertSee(__('user_email.error_must_be_one_for_rescue'));

			$this->assertTrue($email->fresh()->rescue);

			$user->emails()
				->save(factory(UserEmail::class)->make(['rescue' => true]));

			// disable rescue

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $email->id . '"]', function ($email) {
					$email->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($menu) {
							$menu->assertSee(__('user_email.rescue_disable'))
								->clickLink(__('user_email.rescue_disable'));
						});
				})->assertSee(__('user_email.now_not_for_rescue', ['email' => $email->email]));

			$this->assertFalse($email->fresh()->rescue);

			// use for rescue

			$browser->with('.email[data-id="' . $email->id . '"]', function ($email) {
				$email->click('.dropdown-toggle')
					->with('.dropdown-menu', function ($menu) {
						$menu->assertSee(__('user_email.rescue_enable'))
							->clickLink(__('user_email.rescue_enable'));
					});
			})->assertSee(__('user_email.now_for_rescue', ['email' => $email->email]));

			$this->assertTrue($email->fresh()->rescue);
		});
	}

	public function testnotificationsEnableAndDisable()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->states('with_confirmed_email')
				->create();

			$email = $user->emails()->get()->first();

			$second_email = factory(UserEmail::class)->create([
				'user_id' => $user->id,
				'notice' => false,
				'confirm' => true
			]);

			$this->assertTrue($email->fresh()->notice);
			$this->assertFalse($second_email->fresh()->notice);

			$browser->resize(1000, 1000)
				->loginAs($user);

			// enable notice for second email

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $second_email->id . '"]', function ($email) {
					$email->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($menu) {
							$menu->assertSee(__('user_email.notice_enable'))
								->clickLink(__('user_email.notice_enable'));
						});
				})->assertSee(__('user_email.now_for_notice', ['email' => $second_email->email]));

			$this->assertFalse($email->fresh()->notice);
			$this->assertTrue($second_email->fresh()->notice);

			// enable notice for first email

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $email->id . '"]', function ($email) {
					$email->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($menu) {
							$menu->assertSee(__('user_email.notice_enable'))
								->clickLink(__('user_email.notice_enable'));
						});
				})->assertSee(__('user_email.now_for_notice', ['email' => $email->email]));

			$this->assertTrue($email->fresh()->notice);
			$this->assertFalse($second_email->fresh()->notice);
		});
	}

	public function testSendConfirmLink()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->states('with_not_confirmed_email')
				->create();

			$email = $user->emails()->get()->first();

			$browser->resize(1000, 1000)
				->loginAs($user);

			// see only delete button

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $email->id . '"]', function ($email) {
					$email->assertSee(__('user_email.not_confirm'))
						->assertSee(__('user_email.confirm'))
						->clickLink(__('user_email.confirm'));
				})
				->assertSee(__('user_email.confirm_url_sended', ['email' => $email->email]));
		});
	}

	public function testTryDeleteConfirmedEmail()
	{
		$this->browse(function ($browser) {

			$user = factory(User::class)
				->states('with_confirmed_email')
				->create();

			$email = $user->emails()->get()->first();

			$browser->resize(1000, 1000)
				->loginAs($user);

			$browser->visit(route('users.emails.index', $user))
				->with('.email[data-id="' . $email->id . '"]', function ($email) {
					$email->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($menu) {
							$menu->assertSee(__('common.delete'))
								->clickLink(__('common.delete'));
						});
				})->assertSee(__('user_email.error_1'));
		});
	}
}
