<?php

namespace Tests\Feature\User\Setting;

use App\User;
use App\UserReadStyle;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserSettingReadStyleTest extends TestCase
{
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

	public function testSettingSaveAsAjax()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->ajax()
			->followingRedirects()
			->post(route('users.settings.read_style.update', $user),
				$user->readStyle->toArray())
			->assertOk()
			->assertSeeText(__('common.data_saved'));
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
