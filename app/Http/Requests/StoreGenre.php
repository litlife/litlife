<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenre extends FormRequest
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
			'name' => 'required|unique:genres,name',
			'fb_code' => 'required|regex:/^[A-Za-z\_]+$/|unique:genres,fb_code',
			'genre_group_id' => 'required|exists:genres,id',
			'age' => 'nullable|numeric|digits_between:6,18'
		];
	}

	public function attributes()
	{
		return __('genre');
	}

	protected function prepareForValidation()
	{
		if (empty($this->age))
			$this->age = null;

		$this->merge([
			'age' => $this->age,
		]);
	}
}
