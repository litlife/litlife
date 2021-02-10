<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class UserNickUnique implements Rule
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
	 * @param int $user_id
	 * @return $this
	 */
	public function ignore_user_id(int $user_id)
	{
		$this->ignore_user_id = $user_id;

		return $this;
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
		$query = User::where('nick', 'ilike', ilikeSpecialChars($value));

		if (!empty($this->ignore_user_id))
			$query->where('id', '!=', $this->ignore_user_id);

		if ($nick = $query->first())
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
		return __('validation.user_nick_unique');
	}
}
