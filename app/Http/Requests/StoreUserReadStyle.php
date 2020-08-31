<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserReadStyle extends FormRequest
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
			'font' => 'required|in:' . implode(',', config('litlife.read_allowed_fonts')),
			'align' => 'required|in:' . implode(',', config('litlife.read_text_align')),
			'size' => 'required|in:' . implode(',', config('litlife.read_font_size')),
			'card_color' => 'nullable|color',
			'background_color' => 'nullable|color',
			'font_color' => 'nullable|color'
		];
	}

	public function attributes()
	{
		return __('user.read_style_array');
	}

	protected function prepareForValidation()
	{
		if (!in_array($this->font, config('litlife.read_allowed_fonts'))) {
			$this->merge([
				'font' => 'Default',
			]);
		}
	}
}
