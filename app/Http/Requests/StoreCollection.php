<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCollection extends FormRequest
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
			'title' => 'required|string|min:2|max:255',
			'description' => 'nullable|string|min:2|max:1000',
			'status' => [
				'required',
				Rule::in(['0', '3']),
			],
			'who_can_add' => 'required|enum_key:' . UserAccountPermissionValues::class,
			'who_can_comment' => 'required|enum_key:' . UserAccountPermissionValues::class,
			'url' => 'url|nullable|max:200',
			'url_title' => 'string|nullable|min:2|max:200'
		];
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param Validator $validator
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			$status = $validator->validated()['status'] ?? '';
			$who_can_add = $validator->validated()['who_can_add'];
			$who_can_comment = $validator->validated()['who_can_comment'];

			if (StatusEnum::Private == $status and $who_can_add != 'me') {
				$validator->errors()->add('who_can_add', __('collection.validation.equals_value_if_other_field_equals',
					[
						'attribute' => __('collection.who_can_add'),
						'value' => __('collection.who_can_add_array.me'),
						'other_attribute' => __('collection.status'),
						'other_value' => __('collection.status_array.' . StatusEnum::Private)
					]));
			}

			if (StatusEnum::Private == $status and $who_can_comment != 'me') {
				$validator->errors()->add('who_can_comment', __('collection.validation.equals_value_if_other_field_equals',
					[
						'attribute' => __('collection.who_can_comment'),
						'value' => __('collection.who_can_comment_array.me'),
						'other_attribute' => __('collection.status'),
						'other_value' => __('collection.status_array.' . StatusEnum::Private)
					]));
			}
		});
	}

	public function attributes()
	{
		return __('collection');
	}
}
