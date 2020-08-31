<?php

namespace App\Authentication;

use App\User;
use App\UserEmail;
use App\UserToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as IlluminateUserProvider;

class UserProvider implements IlluminateUserProvider
{
	/**
	 * @param mixed $identifier
	 * @return Authenticatable|null
	 */
	public function retrieveById($identifier)
	{
		// Get and return a user by their unique identifier
		return User::find($identifier);
	}

	/**
	 * @param mixed $identifier
	 * @param string $token
	 * @return Authenticatable|null
	 */
	public function retrieveByToken($identifier, $token)
	{
		// Get and return a user by their unique identifier and "remember me" token
		/*
				return User::find($identifier)
					->where('remember_token', $token)->first();
		*/

		$user_token = UserToken::where('user_id', $identifier)
			->where('token', $token)->first();

		if (!empty($user_token->user))
			return $user_token->user;

	}

	/**
	 * @param Authenticatable $user
	 * @param string $token
	 * @return void
	 */
	public function updateRememberToken(Authenticatable $user, $token)
	{
		// Save the given "remember me" token for the given user
		/*
		$user->remember_token = $token;
		$user->save();
		*/

		UserToken::updateOrCreate(
			['user_id' => $user->id],
			['token' => $token]
		);
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param array $credentials
	 * @return Authenticatable|null
	 */
	public function retrieveByCredentials(array $credentials)
	{
		// Get and return a user by looking up the given credentials

		if (is_numeric($credentials['login'])) {
			$user = User::find($credentials['login']);
		} else {
			$email = UserEmail::whereEmail($credentials['login'])
				->confirmed()
				->first();

			if (!empty($email))
				$user = $email->user;
		}

		return $user ?? null;
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param Authenticatable $user
	 * @param array $credentials
	 * @return bool
	 */
	public function validateCredentials(Authenticatable $user, array $credentials)
	{
		// Check that given credentials belong to the given user

		if (md0($credentials['password']) == $user->getAuthPassword())
			return TRUE;
		else
			return FALSE;
	}

}