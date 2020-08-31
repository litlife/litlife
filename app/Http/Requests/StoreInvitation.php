<?php

namespace App\Http\Requests;

use App\Rules\UserEmail;
use App\Rules\UserEmailUnique;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvitation extends FormRequest
{
	protected $errorBag = 'invitation';

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'g-recaptcha-response' => 'required|captcha',
			'email' => [
				'bail',
				'required',
				'email',
				'tempmail',
				new UserEmailUnique
			]
		];
	}


	public function attributes()
	{
		return __('user');
	}
}
