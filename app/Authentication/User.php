<?php

namespace App\Authentication;

use App\UserToken;

trait User
{
	/**
	 * @return string
	 */
	public function getAuthIdentifierName()
	{
		// Return the name of unique identifier for the user (e.g. "id")
		return 'id';
	}

	/**
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		// Return the unique identifier for the user (e.g. their ID, 123)

		return $this->attributes['id'];
	}

	/**
	 * @return string
	 */
	public function getAuthPassword()
	{
		// Returns the (hashed) password for the user
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getRememberToken()
	{
		// Return the token used for the "remember me" functionality

		return optional($this->token)->token;
	}

	/**
	 * @param string $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		// Store a new token user for the "remember me" functionality

		ignoreDuplicateException(function () use ($value) {
			UserToken::updateOrCreate(
				['user_id' => $this->attributes['id']],
				['token' => $value]
			);
		});
	}

	/**
	 * @return string
	 */
	public function getRememberTokenName()
	{
		// Return the name of the column / attribute used to store the "remember me" token
		return "token";
	}
}
