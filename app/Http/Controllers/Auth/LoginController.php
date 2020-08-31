<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\UserEmail;
use App\UserSetting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers {
		logout as performLogout;
	}

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	public function __construct()
	{
		$this->redirectTo = url()->previous();

		$this->middleware('guest', ['except' => 'logout']);

		$this->maxAttempts = config('auth.max_attempts');
		$this->decayMinutes = config('auth.decay_minutes');
	}

	/**
	 * Show the application's login form.
	 *
	 * @return Response
	 */
	public function showLoginForm()
	{
		if (request()->ajax())
			return view('auth.form');
		else
			return view('auth.login');
	}

	public function logout(Request $request)
	{
		$this->performLogout($request);

		return redirect()
			->route('home');
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response|JsonResponse
	 *
	 * @throws ValidationException
	 */
	public function login(Request $request)
	{
		$this->validateLogin($request);

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if (method_exists($this, 'hasTooManyLoginAttempts') &&
			$this->hasTooManyLoginAttempts($request)) {

			$seconds = $this->limiter()->availableIn(
				$this->throttleKey($request)
			);

			if ($seconds < 0) {
				$this->clearLoginAttempts($request);
			} else {
				$this->fireLockoutEvent($request);

				$minutes = ceil($seconds / 60);

				return back()
					->withInput($request->only($this->username(), 'remember'))
					->withErrors(['login' => trans_choice('auth.throttle', $minutes, ['minutes' => $minutes])], 'login');
			}
		}

		if (is_numeric($request->input($this->username()))) {
			$setting = UserSetting::where('user_id', intval($request->input($this->username())))
				->where('login_with_id', true)
				->first();

			if (empty($setting) or empty($setting->user)) {
				return back()
					->withInput($request->only($this->username(), 'remember'))
					->withErrors(['login' => __('user_email.user_with_id_not_found')], 'login');
			} else {
				$user = $setting->user;
			}
		} else {

			$email = UserEmail::whereEmail($request->input($this->username()))
				->confirmed()->first();

			if (empty($email)) {

				$email = UserEmail::whereEmail($request->input($this->username()))
					->createdBeforeMoveToNewEngine()
					->unconfirmed()
					->first();

				if (!empty($email)) {
					$user = $email->user;
				} else {
					// if confirmed email not found
					$email = UserEmail::whereEmail($request->input($this->username()))
						->unconfirmed()
						->first();

					if (!empty($email)) {

						// if unconfirmed email found then
						return back()
							->withInput($request->only($this->username(), 'remember'))
							->with(['unconfirmed' => true, 'email' => $email])
							->withErrors(['login' => __('user_email.not_confirmed')], 'login');
					} else {
						// if unconfirmed email not found throw error
						return back()
							->withInput($request->only($this->username(), 'remember'))
							->withErrors(['login' => __('user_email.nothing_found')], 'login');
					}
				}
			} else {
				$user = $email->user;
			}
		}
		/*
				if (empty($email)) {
					return back()
						->withInput($request->only($this->username(), 'remember'))
						->withErrors([__('user_email.nothing_found')], 'login');
				}
				*/
		if (empty($user) or $user->trashed())
			return back()
				->withInput($request->only($this->username(), 'remember'))
				->withErrors(['login' => __('user.deleted')], 'login');

		if ($user->isSuspended())
			return back()
				->withInput($request->only($this->username(), 'remember'))
				->with(['you_account_suspended_try_recover_password' => true])
				->withErrors(['login' => __('user.suspended')], 'login');

		$credentials = [
			'login' => $user->id,
			'password' => $request->login_password
		];

		if ($this->attemptLogin($request, $credentials))
			return $this->sendLoginResponse($request);

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

	protected function validateLogin(Request $request)
	{
		$this->validateWithBag('login', $request, [
			$this->username() => 'required|string',
			'login_password' => 'required|string',
		], [],
			[
				$this->username() => trans_choice('user.email', 1),
				'login_password' => __('user.password')
			]);
	}

	public function username()
	{
		return 'login';
	}

	/**
	 * Attempt to log the user into the application.
	 *
	 * @param Request $request
	 * @return bool
	 */
	protected function attemptLogin(Request $request, $credentials)
	{
		return $this->guard()->attempt(
			$credentials, $request->filled('remember')
		);
	}

	protected function sendFailedLoginResponse(Request $request)
	{
		return back()->withInput()
			->with(['failed' => true])
			->withErrors(['login' => __('auth.failed')], 'login');
	}
}
