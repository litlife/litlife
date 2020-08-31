<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestion extends FormRequest
{
	protected $errorBag = 'question';

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
			'name' => 'required|min:2|max:200',
			'bb_text' => 'required|string|min:30',
			'notify_about_responses' => 'boolean|required'
		];
	}


	public function attributes()
	{
		return __('question');
	}
}
