<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopic extends FormRequest
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
			'post_desc' => 'sometimes|boolean',
			'forum_priority' => 'sometimes|integer',
			'first_post_on_top' => 'sometimes|boolean',
			'main_priority' => 'sometimes|integer',
			'hide_from_main_page' => 'sometimes|boolean'
		];
	}


	public function attributes()
	{
		return __('topic');
	}
}
