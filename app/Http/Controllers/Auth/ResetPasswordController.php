<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUser;
use App\PasswordReset;
use App\User;
use Auth;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Password;

class ResetPasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	/**
	 * Where to redirect users after resetting their password.
	 *
	 * @var string
	 */
	//protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * If no token is present, display the link request form.
	 *
	 * @param Request $request
	 * @param string|null $token
	 * @return Factory|View
	 */
	public function showResetForm($token)
	{
		$reset = PasswordReset::token($token)->first();

		if (empty($reset))
			return redirect()
				->route('password.request')
				->withErrors(__('password_reset.this_password_reset_link_is_outdated_or_entered_incorrectly'), 'email');

		if ($reset->isUsed())
			return redirect()
				->route('password.request')
				->withErrors(__('password_reset.this_password_reset_link_has_already_been_used'), 'email');

		return view('auth.passwords.reset', compact('reset'));
	}

	/**
	 * Reset the given user's password.
	 *
	 * @param Request $request
	 * @throws
	 */
	public function reset(Request $request)
	{
		$this->validateWithBag('password_reset', $request, $this->rules(), [], __('user'));

		$count = User::wherePassword($request->password)->count();

		if ($count >= config('auth.max_frequent_password_count')) {
			return back()
				->withErrors(['password' => __('password.frequent')], 'password_reset');
		}

		$reset = PasswordReset::token($request->token)->first();

		if (empty($reset))
			return redirect()
				->route('password.request')
				->withErrors(__('password_reset.this_password_reset_link_is_outdated_or_entered_incorrectly'), 'email');

		if ($reset->isUsed())
			return redirect()
				->route('password.request')
				->withErrors(__('password_reset.this_password_reset_link_has_already_been_used'), 'email');

		$user = $reset->user;

		if (empty($user))
			return redirect()
				->route('password.request')
				->withErrors(__('user_email.error_user_not_found'), 'email')
				->withInput();

		if ($user->trashed())
			return redirect()
				->route('password.request')
				->withErrors(__('user_email.error_user_deleted'), 'email')
				->withInput();

		$user->password = $request->password;
		$user->setRememberToken(Str::random(60));
		$user->unsuspend();
		$user->save();

		$not_confirmed_email = $user->emails()
			->unconfirmed()
			->whereEmail($reset->email)
			->first();

		if (!empty($not_confirmed_email)) {
			$not_confirmed_email->confirmEmail();
		}

		// обновляем время когда использован токен
		$reset->used();
		// удаляем запись
		$reset->delete();

		event(new PasswordResetEvent($user));

		Auth::guard()->login($user);

		return redirect()
			->route('profile', $user);

		/*
		// Here we will attempt to reset the user's password. If it is successful we
		// will update the password on an actual user model and persist it to the
		// database. Otherwise we will parse the error and return the response.
		$response = $this->broker()->reset(
			$this->credentials($request), function ($user, $password) {
				$this->resetPassword($user, $password);
			}
		);

		// If the password was successfully reset, we will redirect the user back to
		// the application's home authenticated view. If there is an error we can
		// redirect them back to where they came from with their error message.
		return $response == Password::PASSWORD_RESET
			? $this->sendResetResponse($response)
			: $this->sendResetFailedResponse($request, $response);
		*/
	}

	/**
	 * Get the password reset validation rules.
	 *
	 * @return array
	 */
	protected function rules()
	{
		return array_merge(
			['token' => 'required'],
			(new StoreUser())->passwordRules()
		);
	}

	/**
	 * Get the broker to be used during password reset.
	 *
	 * @return PasswordBroker
	 */
	public function broker()
	{
		return Password::broker();
	}

	/**
	 * Get the password reset validation error messages.
	 *
	 * @return array
	 */
	protected function validationErrorMessages()
	{
		return [];
	}

	/**
	 * Get the password reset credentials from the request.
	 *
	 * @param Request $request
	 * @return array
	 */
	protected function credentials(Request $request)
	{
		return $request->only(
			'password', 'password_confirmation', 'token', 'login_email'
		);
	}

	/**
	 * Reset the given user's password.
	 *
	 * @param CanResetPassword $user
	 * @param string $password
	 * @return void
	 */
	protected function resetPassword($user, $password)
	{
		$user->password = md0($password);

		$user->setRememberToken(Str::random(60));

		$user->save();

		event(new PasswordReset($user));

		$this->guard()->login($user);
	}

	/**
	 * Get the response for a successful password reset.
	 *
	 * @param string $response
	 * @return RedirectResponse
	 */
	protected function sendResetResponse($response)
	{
		return redirect($this->redirectPath())
			->with('status', __($response));
	}

	/**
	 * Get the response for a failed password reset.
	 *
	 * @param Request
	 * @param string $response
	 * @return RedirectResponse
	 */
	protected function sendResetFailedResponse(Request $request, $response)
	{
		return back()
			->withInput($request->only('login_email'))
			->withErrors(['login_email' => __($response)]);
	}

}
