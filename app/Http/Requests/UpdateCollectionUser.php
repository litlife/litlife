<?php

namespace App\Http\Requests;

use App\CollectionUser;

class UpdateCollectionUser extends StoreCollectionUser
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$collectionUser = new CollectionUser();

		$array = [
			'description' => 'nullable|string|max:100'
		];

		foreach ($collectionUser->getPermissions() as $name => $value) {
			$array[$name] = 'required|boolean';
		}

		return $array;
	}
}
