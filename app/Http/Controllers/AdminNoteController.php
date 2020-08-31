<?php

namespace App\Http\Controllers;

use App\AdminNote;
use App\Http\Requests\StoreAdminNote;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminNoteController extends Controller
{
	public function index()
	{
		$this->authorize('view', AdminNote::class);

		$type = request()->type;
		$id = request()->id;

		if (empty($type) and empty($id)) {
			$notes = AdminNote::with('create_user')
				->latest()
				->simplePaginate();

			return view('admin_note.index', compact('notes'));
		} else {
			$admin_noteable = $this->getObjectOrError(request()->type, request()->id);

			$notes = $admin_noteable->admin_notes()
				->with('create_user')
				->latest()
				->simplePaginate();

			return view('admin_note.index', compact('notes', 'type'));
		}
	}

	public function getObjectOrError($type, $id)
	{
		$map = Relation::morphMap();

		if (!isset($map[$type]))
			abort(404);
		else
			$model = $map[$type];

		return $model::findOrFail($id);
	}

	/**
	 * Отображение формы
	 *
	 * @return View
	 * @throws
	 */

	public function create()
	{
		$this->authorize('create', AdminNote::class);

		$admin_noteable = $this->getObjectOrError(request()->type, request()->id);

		$type = request()->type;

		return view('admin_note.create', compact('admin_noteable', 'type'));
	}

	/**
	 * Сохранение формы
	 *
	 * @param StoreAdminNote $request
	 * @return Response
	 * @throws
	 */

	public function store(StoreAdminNote $request)
	{
		$this->authorize('create', AdminNote::class);

		$admin_noteable = $this->getObjectOrError(request()->type, request()->id);

		$note = new AdminNote;
		$note->fill($request->all());
		$admin_noteable->admin_note()->save($note);

		return redirect()
			->route('admin_notes.index', [
				'type' => $note->admin_noteable_type,
				'id' => $note->admin_noteable_id
			]);
	}

	/**
	 * Форма редактирования записи
	 *
	 * @param AdminNote $admin_note
	 * @return View
	 * @throws
	 */

	public function edit(AdminNote $admin_note)
	{
		$this->authorize('update', $admin_note);

		return view('admin_note.edit', compact('admin_note'));
	}

	/**
	 * Редактирование записи
	 *
	 * @param StoreAdminNote $request
	 * @param AdminNote $admin_note
	 * @return Response
	 * @throws
	 */

	public function update(StoreAdminNote $request, AdminNote $admin_note)
	{
		$this->authorize('update', $admin_note);

		$admin_note->fill($request->all());
		$admin_note->user_edited_at = now();
		$admin_note->save();

		return redirect()
			->route('admin_notes.index', [
				'type' => $admin_note->admin_noteable_type,
				'id' => $admin_note->admin_noteable_id
			]);
	}

	/**
	 * Удаление записи
	 *
	 * @param AdminNote $admin_note
	 * @return Response
	 * @throws
	 */

	public function destroy(AdminNote $admin_note)
	{
		$this->authorize('delete', $admin_note);

		$admin_note->delete();

		return redirect()
			->route('admin_notes.index', [
				'type' => $admin_note->admin_noteable_type,
				'id' => $admin_note->admin_noteable_id
			]);
	}
}
