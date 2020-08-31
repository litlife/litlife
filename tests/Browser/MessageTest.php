<?php

namespace Tests\Browser;

use App\Enums\UserAccountPermissionValues;
use App\Message;
use App\User;
use Tests\DuskTestCase;

class MessageTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testCreate()
	{
		$this->browse(function ($browser, $browser2) {

			$user = factory(User::class)->create();

			$user2 = factory(User::class)->create();

			$text = $this->faker->realText(100);

			$user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
			$user->account_permissions->save();

			$user2->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
			$user2->account_permissions->save();

			$this->assertEquals(UserAccountPermissionValues::friends_and_subscribers,
				$user->account_permissions->write_private_messages);

			$this->assertEquals(UserAccountPermissionValues::friends_and_subscribers,
				$user2->account_permissions->write_private_messages);

			$this->assertFalse($user->can('write_private_messages', $user2));

			// cant send
			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.messages.index', $user2))
				->with('#main', function ($main) {
					$main->assertMissing('form');
				});


			$user2->account_permissions->write_private_messages = UserAccountPermissionValues::everyone;
			$user2->account_permissions->save();

			$this->assertTrue($user->can('write_private_messages', $user2));

			// can send
			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('users.messages.index', $user2))
				->assertVisible('form')
				->waitFor('.sceditor-container', 15)
				->driver
				->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("' . $text . '");');

			$browser->press(__('common.send'))
				->whenAvailable('.messages', function ($messages) use ($text) {
					$messages->assertSee($text);
				});

			$browser2->resize(1000, 1000)
				->loginAs($user2)
				->visit(route('profile', $user2))
				->whenAvailable('a.list-group-item[href="' . route('users.inbox', $user2) . '"] .badge', function ($badge) {
					$badge->assertSee('1');
				})
				->visit(route('users.inbox', $user2))
				->whenAvailable('#main [data-id="' . $user->id . '"]', function ($item) use ($text) {
					$item->with('.badge', function ($badge) {
						$badge->assertSee(1);
					});
				})
				->click('#main [data-id="' . $user->id . '"]')
				->assertSee($text)
				->visit(route('users.inbox', $user2));
		});
	}

	public function testDeleteAndRestore()
	{
		$this->browse(function ($browser, $browser2) {

			$recepient = factory(User::class)->create()->fresh();

			$message = factory(Message::class)
				->create([
					'recepient_id' => $recepient->id
				])
				->fresh();

			$browser2->resize(1000, 1000)
				->loginAs($recepient)
				->visit(route('profile', $recepient))
				->whenAvailable('a.list-group-item[href="' . route('users.inbox', $recepient) . '"] .badge', function ($badge) {
					$badge->assertSee('1');
				});

			$browser->resize(1000, 1000)
				->loginAs($message->create_user)
				->visit(route('users.messages.index', $recepient))
				->with('.item[data-id="' . $message->id . '"]', function ($item) {
					$item->click('.dropdown-toggle')
						->assertVisible('.dropdown-menu')
						->with('.dropdown-menu', function ($dropdown_menu) {
							$dropdown_menu->clickLink(__('common.delete'));
						})
						->click('.dropdown-toggle')
						->with('.dropdown-menu', function ($dropdown_menu) {
							$dropdown_menu->waitForText(mb_strtolower(__('common.restore')));
						})
						->click('.dropdown-toggle')
						->assertMissing('.dropdown-menu');
				});


			$browser->with('.item[data-id="' . $message->id . '"]', function ($item) {
				$item->assertVisible('.dropdown-toggle')
					->click('.dropdown-toggle')
					->assertVisible('.dropdown-menu')
					->with('.dropdown-menu', function ($dropdown_menu) {
						$dropdown_menu->clickLink(__('common.restore'));
					})
					->click('.dropdown-toggle')
					->with('.dropdown-menu', function ($dropdown_menu) {
						$dropdown_menu->waitForText(mb_strtolower(__('common.delete')));
					});
			});

			$browser2->visit(route('profile', $recepient))
				->whenAvailable('a.list-group-item[href="' . route('users.inbox', $recepient) . '"] .badge', function ($badge) {
					$badge->assertSee('1');
				});
		});
	}

	public function testViewMessage()
	{
		$this->browse(function ($sender_browser, $recepient_browser) {

			$recepient = factory(User::class)
				->create()
				->fresh();

			$message_viewed = factory(Message::class)
				->states('viewed')
				->create([
					'recepient_id' => $recepient->id
				])
				->fresh();

			$message = factory(Message::class)
				->create([
					'create_user_id' => $message_viewed->create_user_id,
					'recepient_id' => $recepient->id
				])
				->fresh();

			$message2 = factory(Message::class)
				->create([
					'create_user_id' => $message_viewed->create_user_id,
					'recepient_id' => $recepient->id
				])
				->fresh();

			$sender_browser->resize(1000, 2000)
				->loginAs($message->create_user)
				->visit(route('users.messages.index', $recepient))
				->with('.item[data-id="' . $message->id . '"]', function ($message) {
					$message->assertVisible('.fa-eye-slash');
				})
				->with('.item[data-id="' . $message2->id . '"]', function ($message) {
					$message->assertVisible('.fa-eye-slash');
				})
				->with('.item[data-id="' . $message_viewed->id . '"]', function ($message) {
					$message->assertMissing('.fa-eye-slash');
				});

			$recepient_browser->resize(1000, 2000)
				->loginAs($recepient)
				->visit(route('users.inbox', $recepient))
				->with('a.list-group-item[href="' . route('users.inbox', $recepient) . '"] .badge', function ($badge) {
					$badge->assertSee('2');
				})
				->visit(route('users.messages.index', $message_viewed->create_user))
				->assertSee(__('message.new_messages'))
				->with('.item[data-id="' . $message->id . '"]', function ($message) {
					$message->assertVisible('.border-info')
						->assertMissing('.fa-eye-slash');
				})
				->with('.item[data-id="' . $message2->id . '"]', function ($message) {
					$message->assertVisible('.border-info')
						->assertMissing('.fa-eye-slash');
				})
				->with('.item[data-id="' . $message_viewed->id . '"]', function ($message) {
					$message->assertMissing('.border-info')
						->assertMissing('.fa-eye-slash');
				});


			$recepient_browser->visit(route('users.messages.index', $message_viewed->create_user))
				->assertDontSee(__('message.new_messages'))
				->with('.item[data-id="' . $message->id . '"]', function ($message) {
					$message->assertMissing('.border-info')
						->assertMissing('.fa-eye-slash');
				})
				->with('.item[data-id="' . $message2->id . '"]', function ($message) {
					$message->assertMissing('.border-info')
						->assertMissing('.fa-eye-slash');
				})
				->with('.item[data-id="' . $message_viewed->id . '"]', function ($message) {
					$message->assertMissing('.border-info')
						->assertMissing('.fa-eye-slash');
				});
		});
	}


}
