<?php

namespace App\Http\Controllers;

use App\BookmarkFolder;
use App\Http\Requests\StoreBookmarkFolder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BookmarkFolderController extends Controller
{
	/**
	 * Папки закладок js
	 * @param User $user
	 * @param Request $request
	 * @return object
	 */
	public function index(User $user, Request $request)
	{
		$order = $user->setting->bookmark_folder_order;

		$folders = $user->bookmark_folders();

		if (count($order) > 0)
			$folders = $folders->orderByField('id', $order)->get();
		else
			$folders = $folders->get();

		if ($request->ajax())
			return $folders;
	}


	/**
	 * Сохранение
	 *
	 * @param StoreBookmarkFolder $request
	 * @param BookmarkFolder $bookmarkFolder
	 * @return Response
	 * @throws
	 */
	public function store(StoreBookmarkFolder $request, BookmarkFolder $bookmarkFolder)
	{
		$this->authorize('create', $bookmarkFolder);

		$bookmarkFolder->fill($request->all());
		$bookmarkFolder->save();

		return back();
	}

	/**
	 * Отображение папки закладок
	 *
	 * @param int $id
	 * @return View
	 * @throws
	 */
	public function show($id)
	{
		$bookmarkFolder = BookmarkFolder::findOrFail($id);

		if (!$bookmarkFolder)
			return redirect()->route('bookmarks.index');

		$this->authorize('view', $bookmarkFolder);

		$user = $bookmarkFolder->create_user;

		$order = $user->setting->bookmark_folder_order;

		$folders = $user->bookmark_folders();

		if (count($order) > 0)
			$folders = $folders->orderByField('id', $order)->get();
		else
			$folders = $folders->get();

		$bookmarks = $bookmarkFolder->bookmarks()
			->latest()
			->paginate();

		$active_folder = $bookmarkFolder;

		return view('bookmark.folder.index', get_defined_vars());
	}

	/**
	 * Форма редактирования
	 *
	 * @param BookmarkFolder $bookmarkFolder
	 * @return View
	 * @throws
	 */
	public function edit(BookmarkFolder $bookmarkFolder)
	{
		$this->authorize('update', $bookmarkFolder);

		return view('bookmark.folder.edit', compact('bookmarkFolder'));
	}

	/**
	 * Сохранение отредактированного
	 *
	 * @param StoreBookmarkFolder $request
	 * @param BookmarkFolder $bookmarkFolder
	 * @return Response
	 * @throws
	 */
	public function update(StoreBookmarkFolder $request, BookmarkFolder $bookmarkFolder)
	{
		$this->authorize('update', $bookmarkFolder);

		$bookmarkFolder->fill($request->all());
		$bookmarkFolder->save();

		return back()->with(['success' => __('common.data_saved')]);
	}

	/**
	 * Удаление и восстановление
	 *
	 * @param int $id
	 * @return BookmarkFolder $folder
	 * @throws
	 */
	public function destroy($id)
	{
		$folder = BookmarkFolder::withTrashed()->findOrFail($id);

		if ($folder->trashed()) {
			$this->authorize('restore', $folder);

			$folder->restore();
		} else {
			$this->authorize('delete', $folder);

			$folder->delete();
		}

		return $folder;
	}

	/**
	 * Сохранение позиций
	 *
	 * @return Response
	 * @throws
	 */
	public function savePosition()
	{
		$this->authorize('save_position', BookmarkFolder::class);

		$order = request()->get('order');

		$bookmarkFolders = auth()->user()
			->bookmark_folders()
			->whereIn('id', $order)
			->orderByField('id', $order)
			->get();

		$sorted = [];

		foreach ($bookmarkFolders as $bookmarkFolder) {
			$sorted[] = $bookmarkFolder->id;
		}

		$setting = auth()->user()->setting;
		$setting->bookmark_folder_order = $sorted;
		$setting->save();

		return view('success', ['success' => __('bookmark_folder.position_saved')])
			->renderSections()['content'];

	}

	/**
	 * Список
	 *
	 * @param
	 * @param
	 * @return View
	 */
	public function list()
	{
		$order = auth()->user()->setting->bookmark_folder_order;

		$folders = auth()->user()
			->bookmark_folders();

		if (count($order) > 0)
			$folders = $folders->orderByField('id', $order)->get();
		else
			$folders = $folders->get();

		return view('bookmark.folder.list', compact('folders'));
	}
}
