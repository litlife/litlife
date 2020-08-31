<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIdea extends FormRequest
{
	protected $errorBag = 'idea';

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
			'name' => 'required|min:2|max:100',
			'bb_text' => 'required|string',
			'enable_notifications_of_new_messages' => 'boolean|required'
		];
	}


	public function attributes()
	{
		return __('idea');
	}
}
