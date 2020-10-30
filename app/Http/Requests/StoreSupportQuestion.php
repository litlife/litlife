<?php

namespace App\Http\Requests;

use App\Enums\SupportQuestionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupportQuestion extends FormRequest
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
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$this->merge([
			'category' => intval($this->category),
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = (new StoreSupportQuestionMessage())
			->rules();

		return [
			'category' => 'required|enum_value:' . SupportQuestionTypeEnum::class,
			'title' => 'nullable|max:100',
			'bb_text' => $rules['bb_text']
		];
	}


	public function attributes()
	{
		return __('support_question');
	}
}
