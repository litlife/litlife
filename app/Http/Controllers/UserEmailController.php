<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserEmail;
use App\Jobs\User\UpdateUserConfirmedMailboxCount;
use App\Notifications\EmailConfirmNotification;
use App\User;
use App\UserEmail;
use App\UserEmailToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class UserEmailController extends Controller
{
	/**
	 * Список почтовых ящиков пользователя
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function index(User $user)
	{
		$this->authorize('view_email_list', $user);

		js_put('user_id', $user->id);

		return view('user.email.index', compact('user'));
	}

	/**
	 * Форма добавления
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */

	public function create(User $user)
	{
		$this->authorize('create_email', $user);

		return view('user.email.create', compact('user'));
	}

	/**
	 * Сохранение
	 *
	 * @param StoreUserEmail $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function store(StoreUserEmail $request, User $user)
	{
		$this->authorize('create_email', $user);

		if ($user->emails()->whereEmail($request->email)->count()) {
			return redirect()
				->route('users.emails.index', compact('user'))
				->withErrors([__('user_email.you_have_already_added_such_a_mailbox')]);
		}

		$email = new UserEmail;
		$email->fill($request->all());
		$user->emails()->save($email);

		return redirect()
			->route('users.emails.index', compact('user'));
	}

	/**
	 * Удаление
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function destroy(User $user, $id)
	{
		$this->authorize('delete_email', $user);

		$email = $user->emails()->findOrFail($id);

		if ($email->isConfirmed()) {
			if ($user->emails()->confirmed()->count() < 2) {
				return redirect()
					->route('users.emails.index', compact('user'))
					->withErrors([__('user_email.error_1')]);
			}
		}

		if ($email->isRescue()) {
			if ($user->emails()->rescuing()->count() < 2) {
				return redirect()
					->route('users.emails.index', compact('user'))
					->withErrors([__('user_email.error_2')]);
			}
		}

		if ($email->isNotice()) {
			if ($user->emails()->notice()->count() < 2) {
				return redirect()
					->route('users.emails.index', compact('user'))
					->withErrors([__('user_email.you_must_have_at_least_one_mailbox_to_send_notifications')]);
			}
		}

		$email->delete();

		return redirect()
			->route('users.emails.index', compact('user'));
	}

	/**
	 * Отправка письма для подтверждения почтового ящика
	 *
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function sendConfirmToken($id)
	{
		$email = UserEmail::findOrFail($id);

		if (!$email->isValid())
			return view('error', ['error' => __('The address of the mailbox has an invalid format. Please add a mailbox with the correct address.')]);

		if ($email->isConfirmed())
			return view('error', ['error' => __('user_email.already_confirmed')]);

		$token = new UserEmailToken;
		$email->tokens()->save($token);

		Notification::route('mail', $email->email)
			->notify(new EmailConfirmNotification($token));

		if (auth()->check())
			return redirect()
				->route('users.emails.index', ['user' => auth()->user()])
				->with('success', __('user_email.confirm_url_sended', ['email' => $email->email]));
		else
			return view('success', ['success' => __('user_email.confirm_url_sended', ['email' => $email->email])]);
	}

	/**
	 * Подтверждение почтового ящика
	 *
	 * @param UserEmail $email
	 * @param string $token
	 * @return Response
	 * @throws
	 */
	public function confirm(UserEmail $email, $token)
	{
		$token = $email->tokens()->where('token', $token)->first();

		if (empty($token->exists))
			return view('error', ['error' => __('user_email.token_not_found')]);

		$email->confirmEmail();

		if (auth()->check())
			auth()->user()->refresh();

		if (auth()->check())
			return redirect()
				->route('users.emails.index', ['user' => auth()->user()])
				->with('success', __('user_email.success_confirmed', ['email' => $email->email]));
		else
			return view('success', ['success' => __('user_email.success_confirmed', ['email' => $email->email])]);
	}

	/**
	 * Отобображать в профиле почтовый ящик
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function show(User $user, $id)
	{
		$this->authorize('email_show_in_profile', $user);

		$email = $user->emails()->findOrFail($id);

		if (!$email->confirm)
			return redirect()
				->route('users.emails.index', compact('user'))
				->withErrors([__('user_email.not_confirmed')]);

		$email->show_in_profile = true;
		$email->save();

		return redirect()
			->route('users.emails.index', compact('user'))
			->with('success', __('user_email.now_showed_in_profile', ['email' => $email->email]));
	}

	/**
	 * Не отобображать в профиле почтовый ящик
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function hide(User $user, $id)
	{
		$this->authorize('email_hide_in_profile', $user);

		$email = $user->emails()->findOrFail($id);
		$email->show_in_profile = false;
		$email->save();

		return redirect()
			->route('users.emails.index', compact('user'))
			->with('success', __('user_email.now_not_showed_in_profile', ['email' => $email->email]));
	}

	/**
	 * Использовать для восстановления пароля
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function rescue(User $user, $id)
	{
		$this->authorize('email_rescue', $user);

		$email = $user->emails()->findOrFail($id);

		if (!$email->confirm)
			return back()->withErrors([__('user_email.not_confirmed')]);

		$email->rescue = true;
		$email->save();

		return redirect()
			->route('users.emails.index', compact('user'))
			->with('success', __('user_email.now_for_rescue', ['email' => $email->email]));
	}

	/**
	 * Не использовать для восстановления пароля
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function unrescue(User $user, $id)
	{
		$this->authorize('email_unrescue', $user);

		$email = $user->emails()->findOrFail($id);

		if (!$email->isConfirmed())
			return redirect()
				->route('users.emails.index', compact('user'))
				->withErrors([__('user_email.not_confirmed')]);

		if ($user->emails()->rescuing()->count() == 1) {
			return redirect()
				->route('users.emails.index', compact('user'))
				->withErrors([__('user_email.error_must_be_one_for_rescue')]);
		}

		$email->rescue = false;
		$email->save();

		return redirect()
			->route('users.emails.index', compact('user'))
			->with('success', __('user_email.now_not_for_rescue', ['email' => $email->email]));
	}

	/**
	 * Включить отправление уведомлений
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function notificationsEnable(User $user, $id)
	{
		$email = $user->emails()->findOrFail($id);

		$this->authorize('notice_enable', $email);

		DB::transaction(function () use ($user, $email) {

			$user->emails()->update(['notice' => false]);

			$email->notice = true;
			$email->save();
		});

		return redirect()
			->route('users.emails.index', compact('user'))
			->with('success', __('user_email.now_for_notice', ['email' => $email->email]));
	}


	public function notice_disable(Request $request, $email)
	{
		if (!$request->hasValidSignature())
			abort(401);

		$email = UserEmail::whereEmail($email)->firstOrFail();
		$email->notice = false;
		$email->save();

		return view('user.email.notice_disabled', compact('email'));
	}
}
