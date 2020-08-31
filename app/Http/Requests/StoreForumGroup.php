<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumGroup extends FormRequest
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
			'image' => 'nullable|image|min:10|max:' . config('litlife.max_image_size') . '|dimensions:min_width=30,min_height=30'
		];
	}


	public function attributes()
	{
		return __('forum_group');
	}
}
