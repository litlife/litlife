<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserSearchSettingController extends Controller
{
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$user = auth()->user();

		$this->authorize('saveBooksSearchSettings', $user);

		$user->booksSearchSettings()
			->updateOrCreate(
				['name' => $request->name],
				['value' => $request->value]
			);

		return ['status' => 'saved'];
	}
}
