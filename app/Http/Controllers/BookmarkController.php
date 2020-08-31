<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BookmarkController extends Controller
{
	/**
	 * Закладки
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	public function index(User $user)
	{
		$this->authorize('bookmarks_view', $user);

		$order = $user->setting->bookmark_folder_order;

		$folders = $user->bookmark_folders();

		if (!empty($order))
			$folders = $folders->orderByField('id', $order)->get();
		else
			$folders = $folders->get();

		$bookmarks = $user->bookmarks()
			->latest()
			->with(['folder'])
			->simplePaginate();

		$active_folder = $folders->first();

		return view('bookmark.folder.index', compact('folders', 'bookmarks'));
	}

	/**
	 * Сохранение закладки
	 *
	 * @param Request $request
	 * @return object
	 * @throws
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'title' => 'required|max:250',
			'url' => 'required'
		], [], __('bookmark'));

		$bookmark = new Bookmark($request->all());

		$user = auth()->user();

		$this->authorize('create_bookmark', User::class);

		if (!empty($request->folder)) {
			$bookmarkFolder = $user->bookmark_folders()->findOrFail($request->folder);

			$this->authorize('create_bookmark', $bookmarkFolder);

			$bookmark = $bookmarkFolder->bookmarks()->save($bookmark);
		} else {
			$bookmark = auth()->user()->bookmarks()->save($bookmark);
		}

		$bookmark->load('folder');

		$bookmark->refresh();

		return $bookmark;
	}

	/**
	 * Форма редактирования
	 *
	 * @param Bookmark $bookmark
	 * @return View
	 * @throws
	 */
	public function edit(Bookmark $bookmark)
	{
		$this->authorize('update', $bookmark);

		$bookmarks_folders = $bookmark->create_user->bookmark_folders;

		return view('bookmark.edit', compact('bookmark', 'bookmarks_folders'));
	}

	/**
	 * Сохранение
	 *
	 * @param Request $request
	 * @param Bookmark $bookmark
	 * @return Response
	 * @throws
	 */
	public function update(Request $request, Bookmark $bookmark)
	{
		$this->validate($request, [
			'title' => 'required|max:250'
		], [], __('bookmark'));

		$this->authorize('update', $bookmark);

		if (!empty($request->folder_id)) {
			$bookmark->create_user
				->bookmark_folders()
				->findOrFail($request->folder_id);
		}

		$bookmark->fill($request->all());
		$bookmark->save();

		return back()
			->with(['success' => __('common.data_saved')]);
	}

	/**
	 * Удаление и восстановление
	 *
	 * @param int $id
	 * @return Bookmark $bookmark
	 * @throws
	 */
	public function destroy($id)
	{
		$bookmark = Bookmark::withTrashed()->findOrFail($id);

		if ($bookmark->trashed()) {
			$this->authorize('restore', $bookmark);

			$bookmark->restore();
		} else {
			$this->authorize('delete', $bookmark);

			$bookmark->delete();
		}

		return $bookmark;
	}
}
