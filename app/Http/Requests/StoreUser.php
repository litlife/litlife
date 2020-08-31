<?php

namespace App\Http\Requests;

use App\Enums\UserNameShowType;
use App\Rules\UserNickUnique;
use BenSampo\Enum\Rules\EnumKey;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
{
	protected $errorBag = 'user';

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
		$rules = $this->getStandartRules();

		if ($user = $this->route('user'))
			array_push($rules['nick'], (new UserNickUnique())->ignore_user_id($user->id));
		else
			array_push($rules['nick'], new UserNickUnique);

		return $rules;
	}

	public function getStandartRules()
	{
		return [
			'nick' => [
				'required_without:last_name,first_name',
				'nullable',
				'min:2',
				'max:20',
				'not_email',
				'does_not_contain_url',
				'alnum_at_least_three_symbols',
				'required_if:name_show_type,Nick,FirstnameNicknameLastname,LastnameNicknameFirstname,NicknameFirstname,FirstnameNickname',
				'regex:/^([^\/\:\?\\\\#\%\=\+\@\;\,\^]+)$/iu'
			],
			'first_name' => [
				'required_with:middle_name',
				'required_with:last_name',
				'nullable', 'min:2', 'max:20',
				'alpha_single_quote',
				'does_not_contain_url',
				'required_if:name_show_type,FullLastNameFirstName,FullFirstNameLastName,FirstnameNicknameLastname,LastnameNicknameFirstname,LastNameFirstName,FirstNameLastName,NicknameFirstname,FirstnameNickname',
				'regex:/^([^\/\:\?\\\\#\%\=\+\@\;\,\^]+)$/iu'
			],
			'last_name' => [
				'required_with:middle_name',
				'nullable', 'min:2', 'max:20',
				'alpha_single_quote',
				'does_not_contain_url',
				'required_if:name_show_type,FullLastNameFirstName,FullFirstNameLastName,FirstnameNicknameLastname,LastnameNicknameFirstname,LastNameFirstName,FirstNameLastName',
				'regex:/^([^\/\:\?\\\\#\%\=\+\@\;\,\^]+)$/iu'
			],
			'middle_name' => [
				'nullable', 'min:2', 'max:20',
				'alpha_single_quote',
				'does_not_contain_url',
				'regex:/^([^\/\:\?\\\\#\%\=\+\@\;\,\^]+)$/iu'
			],
			'gender' => 'gender',
			'name_show_type' => ['required', new EnumKey(UserNameShowType::class)],
			'born_date' => 'date|nullable|before:' . Carbon::now()->subYears(8)->toDateString() . '|after:' . Carbon::now()->subYears(100)->toDateString(),
			'born_date_show' => 'born_date_show',
			'url_address' => 'unique:users,url_address|nullable|min:3|max:32|regex:[A-z0-9]',
			'born_day' => 'required_with:born_month,born_year',
			'born_month' => 'required_with:born_day,born_year',
			'born_year' => 'required_with:born_day,born_month',
		];
	}

	public function passwordRules()
	{
		return [
			'password' => 'required|confirmed|min:' . config('litlife.min_password_length') . '|regex:/^(?=.*?[[:alpha:]])(?=.*?[0-9]).{6,}$/iu'
		];
	}

	public function withValidator($validator)
	{
		/*
		$validator->after(function ($validator) {
			if (request()->name_show_type == 'Nick')
			{
				if (empty(request()->nick))
				{
					$validator->errors()->add('nick', 'Ник должен быть заполнен, если выбрано отображение в имени только ника');
				}
			}

			if (in_array(request()->name_show_type, ['LastNameFirstName', 'FirstNameLastName']))
			{
				if (empty(request()->first_name) and empty(request()->last_name))
				{
					$validator->errors()->add('last_name', 'Имя и фамилия должны быть заполнены, если выбрано отображение в имени только имени и фамилии');
				}
			}


		});
		*/
	}

	public function attributes()
	{
		return __('user');
	}

	protected function prepareForValidation()
	{
		if ($this->born_day and $this->born_month and $this->born_year) {
			$born_date = $this->born_day . '-' . $this->born_month . '-' . $this->born_year;
		} else {
			$born_date = null;
		}

		$this->merge([
			'born_date' => $born_date,
		]);
	}
}
