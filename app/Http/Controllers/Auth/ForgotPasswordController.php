<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetNotification;
use App\PasswordReset;
use App\UserEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class ForgotPasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset emails and
	| includes a trait which assists in sending these notifications from
	| your application to your users. Feel free to explore this trait.
	|
	*/

	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Display the form to request a password reset link.
	 *
	 * @return Response
	 */
	public function showLinkRequestForm()
	{
		$validator = Validator::make(['email' => request()->email], ['email' => 'email']);

		if (!$validator->fails())
			$email = request()->email;

		return view('auth.passwords.email', ['email' => $email ?? null]);
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws
	 */

	public function sendResetLinkEmail(Request $request)
	{
		$this->validateWithBag('email', $request, [
			'email' => [
				'required', 'email'
			],
			'g-recaptcha-response' => 'required|captcha'
		], [], __('user_email'));


		$email = UserEmail::email($request->input('email'))
			->confirmed()
			->first();

		if (empty($email)) {
			$email = UserEmail::whereEmail($request->input('email'))
				->createdBeforeMoveToNewEngine()
				->unconfirmed()
				->first();

			if (empty($email)) {
				return redirect()->route('password.request')
					->withErrors(__('auth.email_not_found'), 'email');
			}
		} else {
			if (!$email->isRescue())
				return redirect()
					->route('password.request')
					->withErrors(__('user_email.mailbox_not_enabled_for_rescue'), 'email')
					->withInput();
		}

		if (empty($email->user))
			return redirect()
				->route('password.request')
				->withErrors(__('user_email.error_user_not_found'), 'email')
				->withInput();

		if ($email->user->trashed())
			return redirect()
				->route('password.request')
				->withErrors(__('user_email.error_user_deleted'), 'email')
				->withInput();


		$reset = PasswordReset::create([
			'user_id' => $email->user->id,
			'email' => $email->email
		]);

		$reset->user->notify(new PasswordResetNotification($reset));

		return redirect()->route('password.request')
			->with('success', __('auth.link_to_password_restore_sended', ['email' => $email->email]));
	}

}
