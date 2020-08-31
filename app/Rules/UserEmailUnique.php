<?php

namespace App\Rules;

use App\UserEmail;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class UserEmailUnique implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param string $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		if ($email = UserEmail::whereEmail($value)
			->where(function ($query) {
				$query->confirmed()
					->orWhere(function (Builder $query) {
						$query->confirmedOrUnconfirmed()
							->createdBeforeMoveToNewEngine();
					});
			})
			->first())
			return false;
		else
			return true;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return __('validation.user_email_unique');
	}
}
