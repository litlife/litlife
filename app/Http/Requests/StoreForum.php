<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForum extends FormRequest
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
			'name' => 'required|min:2|max:100',
			'description' => 'required|min:2|max:200',
			'min_message_count' => 'required|integer',
			'private' => 'boolean',
			'private_users' => 'required_if:private,1|array|nullable',
		];
	}


	public function attributes()
	{
		return __('forum');
	}
}
