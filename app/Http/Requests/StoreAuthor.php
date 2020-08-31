<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthor extends FormRequest
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
			'first_name' => 'required_with:last_name|nullable|min:1|max:255|required_without:nickname',
			'last_name' => 'required_with:first_name|nullable|min:1|max:255|required_without:nickname',
			'middle_name' => 'sometimes|nullable|min:2|max:255',
			'nickname' => 'required_without:last_name,first_name|nullable|min:2|max:255',
			'lang' => 'exists:languages,code',
			'home_page' => 'sometimes|nullable|url|max:255',
			'email' => 'sometimes|nullable|email|tempmail',
			'wikipedia_url' => 'sometimes|nullable|wikipedia|url',
			'gender' => 'gender',
			'born_date' => 'sometimes|nullable|min:2|max:255',
			'born_place' => 'sometimes|nullable|min:2|max:255',
			'dead_date' => 'sometimes|nullable|min:2|max:255',
			'dead_place' => 'sometimes|nullable|min:2|max:255',
			'years_creation' => 'sometimes|nullable|min:2|max:255',
			'orig_last_name' => 'sometimes|nullable|min:2|max:255',
			'orig_first_name' => 'sometimes|nullable|min:2|max:255',
			'orig_middle_name' => 'sometimes|nullable|min:2|max:255',
			'biography' => 'sometimes|nullable|min:2|max:65000'
		];
	}

	public function attributes()
	{
		return __('author');
	}

	protected function prepareForValidation()
	{
		$string = trim(strip_tags($this->biography, '<img>'));

		if (empty($string)) {
			$this->merge([
				'biography' => null
			]);
		}
	}
}
