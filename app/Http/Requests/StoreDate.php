<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Foundation\Http\FormRequest;

class StoreDate extends FormRequest
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
		$this->replace([
			'year' => intval($this->year),
			'month' => intval($this->month),
			'day' => intval($this->day),
			'hour' => intval($this->hour),
			'minute' => intval($this->minute),
			'second' => intval($this->second),
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
			'year' => 'integer|required|min:1900|max:' . now()->year,
			'month' => 'integer|required|min:1|max:12',
			'day' => 'integer|required|min:1|max:31',
			'hour' => 'integer|required|min:0|max:23',
			'minute' => 'integer|required|min:0|max:60',
			'second' => 'integer|required|min:0|max:60'
		];
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param \Illuminate\Validation\Validator $validator
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {

			if (!$this->validateTime()) {
				$validator->errors()->add('year', __('Date is incorrect'));
			}
		});
	}

	public function validateTime()
	{
		try {
			$date = Carbon::createSafe($this->year, $this->month, $this->day,
				$this->hour, $this->minute, $this->second, $this->session()->get('geoip')->timezone);

			return true;

		} catch (InvalidDateException $exeption) {

			return false;
		}
	}

	public function attributes()
	{
		return [
			'year' => __('Year'),
			'month' => __('Month'),
			'day' => __('Day'),
			'hour' => __('Hour'),
			'minute' => __('Minute'),
			'second' => __('Second'),
		];
	}
}
