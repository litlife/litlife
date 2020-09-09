<?php

namespace Tests\Feature\User\Setting;

use App\Enums\UserAccountPermissionValues;
use App\Genre;
use App\User;
use Tests\TestCase;

class UserSettingTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSiteApperanceHttp()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.settings.site_appearance', $user))
			->assertOk()
			->assertSeeText(__('user_setting.font_size_px'))
			->assertSeeText(__('user_setting.font_family'));

		$this->actingAs($user)
			->followingRedirects()
			->post(route('users.settings.site_appearance.update', $user),
				[
					'login_with_id' => true,
					'font_size_px' => '12',
					'font_family' => 'Arial'

				])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$setting = $user->setting()->first();

		$this->assertTrue($setting->login_with_id);
		$this->assertEquals('12', $setting->font_size_px);
		$this->assertEquals('Arial', $setting->font_family);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('users.settings.site_appearance.update', $user),
				[
					'login_with_id' => false,
					'font_size_px' => '18',
					'font_family' => 'Trebuchet MS'

				])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$setting = $user->setting()->first();

		$this->assertFalse($setting->login_with_id);
		$this->assertEquals('18', $setting->font_size_px);
		$this->assertEquals('Trebuchet MS', $setting->font_family);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('users.settings.site_appearance.update', $user),
				[
					'login_with_id' => false,
					'font_size_px' => '18',
					'font_family' => ''

				])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$setting = $user->setting()->first();

		$this->assertFalse($setting->login_with_id);
		$this->assertEquals('18', $setting->font_size_px);
		$this->assertNull($setting->font_family);
	}

	public function testOtherHttp()
	{
		$user = factory(User::class)
			->create();
		$user->setting->login_with_id = false;
		$user->push();

		$this->actingAs($user)
			->get(route('settings.other', $user))
			->assertSeeText(__('user_setting.login_with_id'))
			->assertOk();;

		$this->actingAs($user)
			->followingRedirects()
			->post(route('settings.other.update', $user),
				[
					'login_with_id' => true

				])
			->assertSeeText(__('common.data_saved'))
			->assertOk();;

		$setting = $user->setting()->first();

		$this->assertTrue($setting->login_with_id);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('settings.other.update', $user),
				[
					'login_with_id' => false

				])
			->assertSeeText(__('common.data_saved'))
			->assertOk();;

		$setting = $user->setting()->first();

		$this->assertFalse($setting->login_with_id);
	}

	public function testGenreBlacklistHttp()
	{
		$user = factory(User::class)
			->create();

		$genre = factory(Genre::class)
			->create();

		$genre2 = factory(Genre::class)
			->create();

		$this->actingAs($user)
			->get(route('genre_blacklist', $user))
			->assertOk();

		$this->actingAs($user)
			->followingRedirects()
			->post(route('genre_blacklist.update', $user),
				[
					'genre' => [$genre->id, $genre2->id]
				])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$this->assertEquals([$genre->id, $genre2->id],
			$user->genre_blacklist()->get()->pluck('id')->toArray());

		$this->actingAs($user)
			->get(route('genre_blacklist', $user))
			->assertOk()
			->assertSeeText($genre->name)
			->assertSeeText($genre2->name);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('genre_blacklist.update', $user),
				[
					'genre' => []
				])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$this->assertEquals(0, $user->genre_blacklist()->get()->count());
	}

	public function testNotifications()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.settings.notifications', $user))
			->assertOk()
			->assertSeeText(__('user_setting.private_message'))
			->assertSeeText(__('user_setting.forum_reply'))
			->assertSeeText(__('user_setting.comment_reply'))
			->assertSeeText(__('user_setting.wall_message'))
			->assertSeeText(__('user_setting.wall_reply'))
			->assertSeeText(__('user_setting.news'));

		$this->assertTrue($user->email_notification_setting->private_message);
		$this->assertTrue($user->email_notification_setting->forum_reply);
		$this->assertTrue($user->email_notification_setting->comment_reply);
		$this->assertTrue($user->email_notification_setting->wall_message);
		$this->assertTrue($user->email_notification_setting->wall_reply);
		$this->assertTrue($user->email_notification_setting->news);

		$this->actingAs($user)
			->followingRedirects()
			->patch(route('users.settings.notifications.update', $user),
				[
					'private_message' => false,
					'forum_reply' => false,
					'wall_message' => false,
					'comment_reply' => false,
					'wall_reply' => false,
					'news' => false,
				])
			->assertSeeText(__('common.data_saved'))
			->assertOk();

		$user->refresh();

		$this->assertFalse($user->email_notification_setting->private_message);
		$this->assertFalse($user->email_notification_setting->forum_reply);
		$this->assertFalse($user->email_notification_setting->comment_reply);
		$this->assertFalse($user->email_notification_setting->wall_message);
		$this->assertFalse($user->email_notification_setting->wall_reply);
		$this->assertFalse($user->email_notification_setting->news);
	}

	public function testAllowance()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('allowance', $user))
			->assertOk()
			->assertSeeText(__('user_setting.write_on_the_wall'))
			->assertSeeText(__('user_setting.comment_on_the_wall'))
			->assertSeeText(__('user_setting.write_private_messages'))
			->assertSeeText(__('user_setting.view_relations'));

		$this->assertEquals(UserAccountPermissionValues::everyone, $user->account_permissions->write_on_the_wall);
		$this->assertEquals(UserAccountPermissionValues::everyone, $user->account_permissions->comment_on_the_wall);
		$this->assertEquals(UserAccountPermissionValues::everyone, $user->account_permissions->write_private_messages);
		$this->assertEquals(UserAccountPermissionValues::everyone, $user->account_permissions->view_relations);

		$this->actingAs($user)
			->followingRedirects()
			->patch(route('allowance.patch', $user),
				[
					'write_on_the_wall' => UserAccountPermissionValues::friends_and_subscribers,
					'comment_on_the_wall' => UserAccountPermissionValues::friends,
					'write_private_messages' => UserAccountPermissionValues::friends_and_subscriptions,
					'view_relations' => UserAccountPermissionValues::me
				])
			->assertSeeText(__('common.data_saved'))
			->assertOk();

		$user->refresh();

		$this->assertEquals(UserAccountPermissionValues::friends_and_subscribers, $user->account_permissions->write_on_the_wall);
		$this->assertEquals(UserAccountPermissionValues::friends, $user->account_permissions->comment_on_the_wall);
		$this->assertEquals(UserAccountPermissionValues::friends_and_subscriptions, $user->account_permissions->write_private_messages);
		$this->assertEquals(UserAccountPermissionValues::me, $user->account_permissions->view_relations);
	}

	public function testLoginWithIdSetting()
	{
		$user = factory(User::class)
			->create();

		$user->setting->loginWithIdEnable();
		$user->setting->save();

		$this->assertTrue($user->setting->isLoginWithIdEnable());
		$this->assertTrue($user->setting->login_with_id);

		$user->setting->loginWithIdDisable();
		$user->setting->save();

		$this->assertFalse($user->setting->isLoginWithIdEnable());
		$this->assertFalse($user->setting->login_with_id);
	}
}
