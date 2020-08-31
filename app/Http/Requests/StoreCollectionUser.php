<?php

namespace App\Http\Requests;

use App\CollectionUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionUser extends FormRequest
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
		$collectionUser = new CollectionUser();

		$array = [
			'user_id' => 'required|integer|exists:users,id',
			'description' => 'nullable|string|max:100'
		];

		foreach ($collectionUser->getPermissions() as $name => $value) {
			$array[$name] = 'required|boolean';
		}

		return $array;
	}

	public function attributes()
	{
		return __('collection_user');
	}
}
