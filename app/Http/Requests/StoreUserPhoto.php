<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPhoto extends FormRequest
{
	protected $errorBag = 'photo';

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
			'file' => 'required|image|dimensions:min_width=100,min_height=100|max:' . config('litlife.max_image_size') . ''
		];
	}

	public function attributes()
	{
		return __('user_photo');
	}
}
