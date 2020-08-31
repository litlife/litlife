<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookTextProcessing extends FormRequest
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
			'remove_bold' => 'required|boolean',
			'remove_extra_spaces' => 'required|boolean',
			'split_into_chapters' => 'required|boolean'
		];
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param Validator $validator
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			if ($this->isAllValueFalse()) {
				$validator->errors()->add('split_into_chapters', __('book_text_processing.at_least_one_item_must_be_marked'));
			}
		});
	}

	public function isAllValueFalse()
	{
		foreach ($this->except('_token') as $key => $value) {
			if (!empty($value))
				return false;
		}

		return true;
	}

	public function attributes()
	{
		return __('book_text_processing');
	}
}
