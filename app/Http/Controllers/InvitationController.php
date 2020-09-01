<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitation;
use App\Http\Requests\StoreRegistrationUser;
use App\Invitation;
use App\Notifications\InvitationNotification;
use App\Notifications\UserHasRegisteredNotification;
use App\User;
use App\UserEmail;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitationController extends Controller
{
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Форма отправки приглашений для регистрации
	 *
	 * @param
	 * @return View
	 */
	public function create()
	{
		return view('invitation');
	}

	/**
	 * Отправка приглашения
	 *
	 * @param StoreInvitation $request
	 * @return Response
	 */
	public function store(StoreInvitation $request)
	{
		do {
			$token = Str::random(32);
		} // Проверим, нет ли уже такого токена, если есть сгенерим заново
		while (Invitation::where('token', $token)->first());

		//создадим запись приглашения
		$invitation = Invitation::create([
			'email' => $request->get('email'),
			'token' => $token
		]);

		// Отправим уведомление
		Notification::route('mail', $invitation->email)
			->notify(new InvitationNotification($invitation));

		/*
		Mail::to($request->get('email'))
			->send(new InvitationCreated($invitation));
  */
		// сделаем редирект обратно
		return redirect()
			->back()
			->withInput()
			->with('ok', 'true');
	}

	/**
	 * Заполнение данных для регистрации пользователя
	 *
	 * @param string $token
	 * @return View
	 */
	public function accept($token)
	{
		if (!$invitation = Invitation::whereToken($token)->first())
			return redirect()
				->route('invitation')
				->withErrors([__('invitation.invitation_not_found_or_expired_please_send_a_new_invitation')], 'invitation');

		return view('user.create', compact('invitation'));
	}

	/**
	 * Сохранение пользователя
	 *
	 * @param StoreRegistrationUser $request
	 * @param string $token
	 * @return Response
	 * @throws
	 */
	public function user(StoreRegistrationUser $request, $token)
	{
		$count = User::wherePassword($request->password)->count();

		if ($count >= config('auth.max_frequent_password_count')) {
			return back()
				->withErrors(['password' => __('password.frequent')], 'registration');
		}

		if (!$invitation = Invitation::whereToken($token)->first())
			return redirect()
				->route('invitation')
				->withErrors([__('invitation.invitation_not_found_or_expired_please_send_a_new_invitation')], 'registration');

		if (UserEmail::whereEmail($invitation->email)
			->confirmed()
			->count())
			return redirect()
				->route('invitation');

		DB::beginTransaction();

		$user = new User;
		$user->email = $invitation->email;
		$user->fill($request->all());
		$user->save();

		$user->setReferredByUserId(Cookie::get(config('litlife.name_user_refrence_get_param')));

		/*
		 * новый пользователь зарегистрирован, поэтому теперь его почтовый ящик должен стать подтвержденным,
		 * использоваться для отправки уведомлений и для восстановления
		 * */

		$email = new UserEmail;
		$email->email = $invitation->email;
		$email->confirm = true;
		$email->rescue = true;
		$email->notice = true;
		$user->emails()->save($email);

		/*
		 * удаляем приглашение
		 */
		$invitation->delete();

		/*
		 * делаем пользователя зарегистрированным
		 */
		Auth::login($user);

		event(new Registered($user));

		$user->notify(new UserHasRegisteredNotification($user, __('password.your_entered_password')));

		DB::commit();

		return redirect()
			->route('welcome');
	}
}
