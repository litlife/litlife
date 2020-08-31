<?php

namespace App\Http\Controllers;

use App\Forum;
use App\ForumGroup;
use App\Http\Requests\StoreForumGroup;
use App\Image;
use App\Variable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ForumGroupController extends Controller
{
	/**
	 * Форма создания группы форумов
	 *
	 * @return View
	 * @throws
	 */

	public function create()
	{
		$this->authorize('create', ForumGroup::class);

		return view('forum.group.create');
	}

	/**
	 * Сохранит новую группу форумов
	 *
	 * @param StoreForumGroup $request
	 * @param ForumGroup $forumGroup
	 * @return Response
	 * @throws
	 */

	public function store(StoreForumGroup $request, ForumGroup $forumGroup)
	{
		$this->authorize('create', $forumGroup);

		$forumGroup->fill($request->all());
		$forumGroup->save();

		return back()->withInput();
	}

	/**
	 * Форма редактирования
	 *
	 * @param ForumGroup $forumGroup
	 * @return View
	 * @throws
	 */
	public function edit(ForumGroup $forumGroup)
	{
		$this->authorize('update', $forumGroup);

		return view('forum.group.edit', compact('forumGroup'));
	}

	/**
	 * Сохранение отредактированного
	 *
	 * @param StoreForumGroup $request
	 * @param ForumGroup $forumGroup
	 * @return Response
	 * @throws
	 */
	public function update(StoreForumGroup $request, ForumGroup $forumGroup)
	{
		$this->authorize('update', $forumGroup);

		$forumGroup->fill($request->all());

		if ($request->hasFile('image')) {
			$image = new Image();
			$image->storage = config('filesystems.default');
			$image->openImage($request->file('image')->getRealPath());
			$image->name = $request->file('image')->getClientOriginalName();
			$image->save();

			$forumGroup->image_id = $image->id;
		}
		$forumGroup->save();

		return back()->withInput();
	}

	/**
	 * Удаление и восстановление
	 *
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function destroy($id)
	{
		$forumGroup = ForumGroup::any()->findOrFail($id);

		if ($forumGroup->trashed()) {
			$this->authorize('restore', $forumGroup);

			$forumGroup->restore();
		} else {
			$this->authorize('delete', $forumGroup);

			$forumGroup->delete();
		}

		return $forumGroup;
	}

	/**
	 * Сохранение перемещенных груп форумов
	 *
	 * @param Request $request
	 * @return void
	 * @throws
	 */

	public function changeOrder(Request $request)
	{
		$this->authorize('change_order', ForumGroup::class);

		$array = ForumGroup::whereIn('id', $request->input('order'))
			->orderByField('id', $request->input('order'))
			->pluck('id')
			->toArray();

		Variable::updateOrCreate(
			['name' => 'ForumGroupSort'],
			['value' => $array]
		);
	}

	/**
	 * Сохранение перемещенных форумов
	 *
	 * @param Request $request
	 * @param ForumGroup $forumGroup
	 * @return void
	 * @throws
	 */

	public function changeForumsOrder(Request $request, ForumGroup $forumGroup)
	{
		$this->authorize('change_order', Forum::class);

		$order = array_filter($request->order);

		$array = Forum::whereIn('id', $order)
			->orderByField('id', $order)
			->pluck('id')
			->toArray();

		$forumGroup->forum_sort = serialize($array);
		$forumGroup->save();
	}
}
