<?php

namespace App\Http\Requests;

use App\Enums\FaceReactionEnum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackSupportQuestion extends FormRequest
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
			'face_reaction' => intval($this->face_reaction),
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'text' => 'nullable|string',
			'face_reaction' => ['required', new EnumValue(FaceReactionEnum::class)],
		];
	}

	public function attributes()
	{
		return __('feedback_support_response');
	}
}
