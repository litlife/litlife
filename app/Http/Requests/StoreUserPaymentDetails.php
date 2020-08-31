<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LVR\CreditCard\CardNumber;

class StoreUserPaymentDetails extends FormRequest
{
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
			'card' => ['nullable', new CardNumber()],
			'wmr' => ['nullable', 'string', 'size:13', 'regex:/^P([0-9]{12})$/iu'],
			'yandex' => 'nullable|integer|digits_between:11,20',
			'qiwi' => 'nullable|phone:AUTO'
		];
	}

	public function attributes()
	{
		return __('user_payment_detail');
	}
}
