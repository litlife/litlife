<?php

namespace App\Http\Requests;

use App\Enums\TextBlockShowEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreTextBlock extends FormRequest
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
		$rules = [
			'text' => 'required',
			'show_for_all' => 'required|in:' . implode(',', TextBlockShowEnum::getValues()) . ''
		];

		return $rules;
	}


	public function attributes()
	{
		return __('text_block');
	}
}
