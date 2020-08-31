<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserReadStyle;
use App\User;
use App\UserReadStyle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class UserSettingController extends Controller
{
	/**
	 * Форма изменения разрешений различных действий
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function allowance(User $user)
	{
		$this->authorize('watch_settings', $user);

		return view('user.setting.allowance', compact('user'));
	}

	/**
	 * Сохранение
	 *
	 * @param User $user
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function allowanceUpdate(User $user, Request $request)
	{
		$this->authorize('update_settings', $user);

		$permissions = $user->account_permissions;

		foreach ($permissions->possible_values as $column => $values) {
			$rules[$column] = 'required|in:' . implode(',', $values);
		}

		$this->validate($request, $rules, [], __('user_setting'));

		$permissions->fill($request->all());
		$permissions->save();

		return back()
			->with('success', __('common.data_saved'));
	}

	/**
	 * Форма изменения настроек отправки уведомлений
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function emailDelivery(User $user)
	{
		$this->authorize('watch_settings', $user);

		return view('user.setting.email_delivery', [
			'user' => $user,
			'url' => route('users.settings.notifications.update', $user)
		]);
	}

	/**
	 * Сохранение
	 *
	 * @param User $user
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function emailDeliveryUpdate(User $user, Request $request)
	{
		$this->authorize('update_settings', $user);

		$user->email_notification_setting->fill($request->all());
		$user->email_notification_setting->save();

		return back()
			->with('success', __('common.data_saved'));
	}

	public function readStyleRedirect()
	{
		return redirect()
			->route('users.settings.read_style', ['user' => auth()->user()]);
	}

	/**
	 * Форма изменения настроек чтения
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function readStyle(User $user)
	{
		$this->authorize('watch_settings', $user);

		if (empty($user->readStyle))
			$style = new UserReadStyle;
		else
			$style = $user->readStyle;

		$vars = [
			'user' => auth()->user(),
			'style' => $style,
			'default_style' => new UserReadStyle
		];

		if (request()->ajax())
			return view('user.setting.read_style_form', $vars);
		else
			return view('user.setting.read_style', $vars);
	}

	/**
	 * Сохранение
	 *
	 * @param User $user
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function readStyleUpdate(StoreUserReadStyle $request, User $user)
	{
		$this->authorize('update_settings', $user);

		$style = UserReadStyle::find($user->id);

		if (blank($style))
			$style = new UserReadStyle;

		$style->fill($request->all());
		$style->user_id = $user->id;
		$style->save();

		$user->readStyle()->save($style);

		if (request()->ajax())
			return view('components.alert', ['type' => 'success', 'text' => __('common.data_saved')]);
		else
			return back()->with(['success' => __('common.data_saved')]);
	}

	/**
	 * Черный список жанров
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function genreBlacklist(User $user)
	{
		$this->authorize('watch_settings', $user);

		return view('user.setting.genre_blacklist', compact('user'));
	}

	/**
	 *  Сохранение
	 *
	 * @param Request $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function genreBlacklistUpdate(Request $request, User $user)
	{
		$this->authorize('update_settings', $user);

		$this->validate($request, ['genre' => 'nullable|array|distinct']);

		$user->genre_blacklist()->sync($request->input('genre'));

		return back()->with('success', __('common.data_saved'));
	}

	/**
	 * Разные настройки
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function other(User $user)
	{
		$this->authorize('watch_settings', $user);

		return view('user.setting.other', compact('user'));
	}

	/**
	 * Разные настройки
	 *
	 * @param Request $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function otherUpdate(Request $request, User $user)
	{
		$this->authorize('watch_settings', $user);

		$this->validate($request, [
			'login_with_id' => 'required|boolean'
		], [], __('user_setting'));

		$user->setting->fill($request->all());
		$user->setting->save();

		return back()->with('success', __('common.data_saved'));
	}

	/**
	 * Форма изменения настроек отправки уведомлений без входа на сайт
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function emailDeliveryWithoutAuthorization(User $user)
	{
		return view('user.setting.email_delivery', [
			'user' => $user,
			'url' => URL::signedRoute('users.settings.email_delivery.update.without_authorization', $user)
		]);
	}

	/**
	 * Сохранение без входа на сайт
	 *
	 * @param User $user
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function emailDeliveryUpdateWithoutAuthorization(User $user, Request $request)
	{
		$user->email_notification_setting->fill($request->all());
		$user->email_notification_setting->save();

		return back()
			->with('success', __('common.data_saved'));
	}

	/**
	 * Настройки внешнего вида сайта
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function siteAppearance(User $user)
	{
		$this->authorize('watch_settings', $user);

		return view('user.setting.site_appearance', compact('user'));
	}

	/**
	 * Сохранение настроек внешнего вида сайта
	 *
	 * @param Request $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function siteAppearanceUpdate(Request $request, User $user)
	{
		$this->authorize('watch_settings', $user);

		$this->validate($request, [
			'font_size_px' => 'required|integer|min:' . config('litlife.font_size.min') . '|max:' . config('litlife.font_size.max') . '',
			'font_family' => 'nullable|in:' . implode(',', config('litlife.available_fonts')),
		], [], __('user_setting'));

		$user->setting->fill($request->all());
		$user->setting->save();

		return back()
			->with('success', __('common.data_saved'));
	}
}
