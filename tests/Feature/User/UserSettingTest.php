<?php

namespace Tests\Feature\User;

use App\Enums\UserAccountPermissionValues;
use App\Genre;
use App\User;
use App\UserReadStyle;
use Illuminate\Support\Facades\DB;
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

	public function testReadStyle()
	{
		$readStyle = (new UserReadStyle());

		$this->assertEquals('Arial', $readStyle->font);
		$this->assertEquals('justify', $readStyle->align);
		$this->assertEquals('18', $readStyle->size);
		$this->assertEquals('#eeeeee', $readStyle->background_color);
		$this->assertEquals('#ffffff', $readStyle->card_color);
		$this->assertEquals('#000000', $readStyle->font_color);

		$readStyle = factory(UserReadStyle::class)
			->create([
				'font' => 'Tahoma',
				'align' => 'right',
				'size' => '16',
				'background_color' => '#333333',
				'card_color' => '#444444',
				'font_color' => '#555555'
			]);

		$user = $readStyle->user;

		$this->assertEquals('Tahoma', $user->readStyle->font);
		$this->assertEquals('right', $user->readStyle->align);
		$this->assertEquals('16', $user->readStyle->size);
		$this->assertEquals('#333333', $user->readStyle->background_color);
		$this->assertEquals('#444444', $user->readStyle->card_color);
		$this->assertEquals('#555555', $user->readStyle->font_color);

		$this->actingAs($user)
			->get(route('users.settings.read_style', $user))
			->assertOk();

		$this->followingRedirects()
			->post(route('users.settings.read_style.update', $user),
				[
					'font' => 'Arial',
					'align' => 'left',
					'size' => '20',
					'background_color' => '#000000',
					'card_color' => '#111111',
					'font_color' => '#222222',

				])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$user->refresh();

		$this->assertEquals('Arial', $user->readStyle->font);
		$this->assertEquals('left', $user->readStyle->align);
		$this->assertEquals('20', $user->readStyle->size);
		$this->assertEquals('#000000', $user->readStyle->background_color);
		$this->assertEquals('#111111', $user->readStyle->card_color);
		$this->assertEquals('#222222', $user->readStyle->font_color);
	}

	public function testIncorrectFontLength()
	{
		$user = factory(User::class)->create();
		$user->readStyle->background_color = '#999999';
		$user->push();
		$user->refresh();

		DB::table('user_read_styles')
			->where('user_id', $user->id)
			->update([
				'background_color' => '0000',
				'card_color' => '0000',
				'font_color' => '0000'
			]);

		$this->assertDatabaseHas('user_read_styles', [
			'user_id' => $user->id,
			'background_color' => '0000',
			'card_color' => '0000',
			'font_color' => '0000'
		]);

		$readStyle = $user->fresh()->readStyle()->disableCache()->first();

		$this->assertEquals('#eeeeee', $readStyle->background_color);
		$this->assertEquals('#ffffff', $readStyle->card_color);
		$this->assertEquals('#000000', $readStyle->font_color);

		DB::table('user_read_styles')
			->where('user_id', $user->id)
			->update([
				'background_color' => '000000',
				'card_color' => '000000',
				'font_color' => '000000'
			]);

		$this->assertDatabaseHas('user_read_styles', [
			'user_id' => $user->id,
			'background_color' => '000000',
			'card_color' => '000000',
			'font_color' => '000000'
		]);

		$readStyle = $user->fresh()->readStyle()->disableCache()->first();

		$this->assertEquals('#000000', $readStyle->background_color);
		$this->assertEquals('#000000', $readStyle->card_color);
		$this->assertEquals('#000000', $readStyle->font_color);
	}

	public function testSantizeFontColor()
	{
		$readStyle = new UserReadStyle();

		$readStyle->background_color = '#0000';
		$this->assertEquals('#eeeeee', $readStyle->background_color);

		$readStyle->card_color = '#0000';
		$this->assertEquals('#ffffff', $readStyle->card_color);

		$readStyle->font_color = '#0000';
		$this->assertEquals('#000000', $readStyle->font_color);
	}

	public function testSettingReadStyleRouteAuthRequired()
	{
		$this->get(route('settings.read_style'))
			->assertStatus(401);
	}

	public function testSettingReadStyleRouteRedirect()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('settings.read_style'))
			->assertRedirect(route('users.settings.read_style', ['user' => $user]));
	}

	public function testReadStyleColorValidationError()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->post(route('users.settings.read_style.update', $user),
				[
					'font' => 'Arial',
					'align' => 'left',
					'size' => '20',
					'background_color' => '#00',
					'card_color' => '#1111',
					'font_color' => '#22222',

				])
			->assertRedirect()
			->assertSessionHasErrors(['background_color' => __('validation.color', ['attribute' => __('user.read_style_array.background_color')])])
			->assertSessionHasErrors(['card_color' => __('validation.color', ['attribute' => __('user.read_style_array.card_color')])])
			->assertSessionHasErrors(['font_color' => __('validation.color', ['attribute' => __('user.read_style_array.font_color')])]);
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

	public function testSetDefaultFontIfWrongValue()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->post(route('users.settings.read_style.update', $user),
				[
					'align' => 'left',
					'size' => '12',
					'background_color' => '#000000',
					'card_color' => '#000000',
					'font_color' => '#000000',

				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$readStyle = $user->fresh()->readStyle()->disableCache()->first();

		$this->assertNotNull($readStyle);

		$this->assertEquals('Default', $readStyle->font);
	}
}
