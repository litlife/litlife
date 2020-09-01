<?php

namespace App\Http\Controllers;

use App\Notifications\UserHasRegisteredNotification;
use App\User;
use App\UserEmail;
use App\UserPhoto;
use App\UserSocialAccount;
use ErrorException;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ImagickException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use RuntimeException;

class UserSocialAccountController extends Controller
{
	/**
	 *
	 * Display a listing of the resource.
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function index(User $user)
	{
		$this->authorize('watch_settings', $user);

		return view('user.social_account.index', compact('user'));
	}

	public function redirectToProvider($provider)
	{
		return Socialite::driver($provider)->redirect();
	}

	public function handleProviderCallback($provider)
	{
		return DB::transaction(function () use ($provider) {

			try {
				try {
					$providerUser = Socialite::driver($provider)->user();
				} catch (InvalidStateException $e) {
					$providerUser = Socialite::driver($provider)->stateless()->user();
				}
			} catch (ClientException $clientException) {
				//dd(json_decode($clientException->getResponse()->getBody()->getContents()));
				return redirect()
					->route('home')
					->withErrors([__('user_social_account.enter_error')], 'login');
			} catch (RuntimeException $exception) {

			} catch (ErrorException | Exception $exception) {

			}

			if (isset($exception))
				return $this->exception($exception);

			$email_array = [];

			if (!empty($providerUser->getEmail())) {
				$email_array[] = $providerUser->getEmail();
			} else {
				if (!empty($providerUser->accessTokenResponseBody['email'])) {
					$email_array[] = $providerUser->accessTokenResponseBody['email'];
				}
			}

			// если пользователь вошел на сайт, то привязываем учетную запись
			if (auth()->check()) {

				$auth_user = auth()->user();

				UserSocialAccount::where('provider', $provider)
					->where('provider_user_id', $providerUser->getId())
					->delete();

				$userSocialAccount = new UserSocialAccount;
				$userSocialAccount->provider = $provider;
				$userSocialAccount->access_token = $providerUser->token;
				$userSocialAccount->provider_user_id = $providerUser->getId();
				$userSocialAccount->parameters = $providerUser;

				$auth_user->social_accounts()->save($userSocialAccount);

				$this->downloadUserPhotoIfNotExists($auth_user, $providerUser->getAvatar());

				return redirect()
					->route('users.social_accounts.index', auth()->user())
					->with(['success' => __('user_social_account.attached', ['provider' => $userSocialAccount->provider])]);
			} else {
				// если нет, то проверяем, существует ли у какого нибудь аккаунта привязка

				$userSocialAccount = UserSocialAccount::where('provider', $provider)
					->where('provider_user_id', $providerUser->getId())
					->first();

				if (!empty($userSocialAccount)) {
					// если привязка есть, то залогиниваем пользователя

					$user = $userSocialAccount->user;

					if (empty($user))
						return redirect()
							->route('home')
							->withErrors([__('user.not_found')], 'login');

					auth()->login($userSocialAccount->user);

					return redirect()
						->route('profile', ['user' => $userSocialAccount->user]);
				} else {

					if (empty($email_array))
						return redirect()
							->route('home')
							->withErrors([__('user_social_account.email_not_found')], 'login');

					$email = UserEmail::whereInEmails($email_array)
						->confirmed()
						->first();

					if (empty($email)) {
						$email = UserEmail::whereInEmails($email_array)
							->createdBeforeMoveToNewEngine()
							->first();
					}

					if (empty($email)) {
						$password = Str::random(8);

						$user = new User;
						preg_match('/([[:graph:]]+)(?:[[:space:]]*)([[:graph:]]*)/iu', $providerUser->getName(), $array);
						list(, $user->first_name, $user->last_name) = $array;
						$user->email = $email_array[0];
						$user->password = $password;
						$user->save();

						$user->setReferredByUserId(Cookie::get(config('litlife.name_user_refrence_get_param')));

						$email = new UserEmail;
						$email->email = $email_array[0];
						$email->confirm = true;
						$email->notice = true;
						$email->rescue = true;
						$user->emails()->save($email);

						$user->notify(new UserHasRegisteredNotification($user, $password));

						$is_new_user = true;
					} else {
						$user = $email->user;

						if (empty($user))
							return redirect()
								->route('home')
								->withErrors([__('user.nothing_found')], 'login');

						if ($user->isSuspended()) {
							$user->unsuspend();
							$user->refresh();
						}

						if ($email->isCreatedBeforeMoveToNewEngine()) {
							$email->confirm = true;
							$email->save();
						}

						$is_new_user = false;
					}

					return $this->createSocialAccount($provider, $providerUser, $user, $is_new_user);
				}
			}
		});
	}

	private function exception($exception)
	{
		if (preg_match('/(Invalid\ JSON\ response\ from\ VK:)(.*)/iu', $exception->getMessage(), $matches)) {
			$json = json_decode($matches[2]);

			if (isset($json->error)) {
				return redirect()
					->route('home')
					->withErrors([__('user_social_account.an_error_occurred', ['error_msg' => $json->error->error_msg])], 'login');
			}

		} elseif ($exception->getMessage() == 'Undefined index: displayName') {
			return redirect()
				->route('home')
				->withErrors([__('user_social_account.google_did_not_report_the_display_name_of_the_user')], 'login');
		} elseif ($exception->getMessage() == 'Undefined index: emails') {
			return redirect()
				->route('home')
				->withErrors([__('user_social_account.email_not_found_allow_use_or_attach_the_mailbox_to_the_social_network')], 'login');
		}

		report($exception);

		return redirect()
			->route('home')
			->withErrors([__('user_social_account.enter_error')], 'login');
	}

	public function downloadUserPhotoIfNotExists($user, $url = null)
	{
		if (empty($user->avatar)) {
			if (!empty($url)) {
				try {
					$photo = new UserPhoto;
					$photo->storage = config('filesystems.default');
					$photo->openImage($url);
					$user->photos()->save($photo);
					$user->avatar()->associate($photo);
					$user->save();

				} catch (ImagickException $exception) {

				}
			}
		}
	}

	public function createSocialAccount($provider, $providerUser, $user, $is_new_user = false)
	{
		// если нет, то извлекаем почтовый ящик

		$userSocialAccount = new UserSocialAccount([
			'provider_user_id' => $providerUser->getId(),
			'provider' => $provider,
			'access_token' => $providerUser->token,
			'parameters' => $providerUser
		]);

		try {
			$user->social_accounts()
				->save($userSocialAccount);
		} catch (QueryException $exception) {
			if ($exception->getCode() == 23505) {
				if (DB::transactionLevel() > 1)
					DB::rollback();

				report($exception);

				return redirect()
					->route('home')
					->withErrors([__('user_social_account.a_login_error_occurred_try_again')], 'login');
			} else {
				throw $exception;
			}
		}

		$this->downloadUserPhotoIfNotExists($user, $providerUser->getAvatar());

		auth()->login($user, true);

		if ($is_new_user) {
			event(new Registered($user));
		}

		if ($is_new_user)
			return redirect()->route('welcome');
		else
			return redirect()->route('profile', compact('user'));
	}

	public function detach(User $user, $id)
	{
		$account = $user->social_accounts()->findOrFail($id);

		$this->authorize('detach', $account);

		$account->delete();

		return back()
			->with(['success' => __('user_social_account.detached', ['provider' => $account->provider])]);
	}
}
