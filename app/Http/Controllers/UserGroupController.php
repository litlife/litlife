<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserGroup;
use App\UserGroup;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserGroupController extends Controller
{
	/**
	 * Список групп
	 *
	 * @return View
	 */
	public function index()
	{
		$this->authorize('view', UserGroup::class);

		$groups = UserGroup::orderBy('id', 'asc')
			->simplePaginate();

		return view('group.index', ['groups' => $groups]);
	}

	/**
	 * Форма добавления
	 *
	 * @return View
	 * @throws
	 */
	public function create()
	{
		$this->authorize('create', UserGroup::class);

		return view('group.create', ['group' => new UserGroup]);
	}

	/**
	 * Сохранение
	 *
	 * @param StoreUserGroup $request
	 * @param UserGroup $group
	 * @return Response
	 * @throws
	 */

	public function store(StoreUserGroup $request, UserGroup $group)
	{
		$this->authorize('create', UserGroup::class);

		$group = new UserGroup;
		$group->fill($request->all());
		$group->save();

		return redirect()
			->route('groups.edit', $group);
	}

	/**
	 * Форма редактирования
	 *
	 * @param UserGroup $group
	 * @return View
	 * @throws
	 */

	public function edit(UserGroup $group)
	{
		$this->authorize('update', $group);

		return view('group.edit', ['group' => $group]);
	}

	/**
	 * Сохранение
	 *
	 * @param StoreUserGroup $request
	 * @param UserGroup $group
	 * @return Response
	 * @throws
	 */

	public function update(StoreUserGroup $request, UserGroup $group)
	{
		$this->authorize('update', $group);

		$group->fill($request->all());
		$group->save();

		return back();
	}

	/**
	 * Удаление или восстановление
	 *
	 * @param integer $id
	 * @return UserGroup $group
	 * @throws
	 */

	public function destroy($id)
	{
		$group = UserGroup::withTrashed()->findOrFail($id);

		if ($group->trashed()) {
			$this->authorize('restore', $group);

			$group->restore();
		} else {
			$this->authorize('delete', $group);

			$group->delete();
		}

		return $group;
	}
}
