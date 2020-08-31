<?php

namespace App\Http\Requests;

use App\Rules\UserNickUnique;

class StoreRegistrationUser extends StoreUser
{
	protected $errorBag = 'registration';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = array_merge($this->getStandartRules(), $this->passwordRules());

		array_push($rules['nick'], new UserNickUnique);

		return $rules;
	}
}
