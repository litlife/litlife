<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;

class UserAgentController extends Controller
{
	/**
	 * Просмотр информации о браузере и ОС
	 *
	 * @param  $model
	 * @param  $id
	 * @return Response
	 * @throws
	 */

	public function show($model, $id)
	{
		$this->authorize('see_technical_information', User::class);

		$map = Relation::morphMap();

		if (!isset($map[$model]))
			abort(404);
		else
			$model = $map[$model];

		$item = $model::any()->find($id);

		$user_agent = $item->user_agent;

		if (request()->ajax())
			return view('user.agent', compact('user_agent'))->renderSections()['content'];
		else
			return view('user.agent', compact('user_agent'));
	}
}
