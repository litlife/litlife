<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookCollected extends FormRequest
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
			'collection_id' => 'required|integer',
			'number' => 'nullable|integer',
			'comment' => 'nullable|string|max:1000'
		];
	}

	public function attributes()
	{
		return __('collected_books');
	}
}
