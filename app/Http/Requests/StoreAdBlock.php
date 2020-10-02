<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\View;

class StoreAdBlock extends FormRequest
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
			'name' => 'required|min:3|max:50',
			'code' => 'required|min:2',
			'description' => 'nullable|string|max:250'
		];
	}

	public function attributes()
	{
		return __('ad_block');
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param \Illuminate\Validation\Validator $validator
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			if (View::exists($this->code)) {
				$validator->errors()->add('code', __('The code cannot be the same as the path to the view file'));
			}
		});
	}
}
