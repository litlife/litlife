<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserNote;
use App\User;
use App\UserNote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserNoteController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(User $user)
	{
		$this->authorize('notes_view', $user);

		$notes = $user->notes()
			->orderBy('updated_at', 'desc')
			->paginate();

		return view('user.note.index', ['user' => $user, 'notes' => $notes]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(User $user)
	{
		$this->authorize('notes_create', $user);

		return view('user.note.create', ['user' => $user]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreUserNote $request, User $user)
	{
		$this->authorize('notes_create', $user);

		DB::beginTransaction();

		$note = new UserNote($request->all());
		$note->save();

		DB::commit();

		return redirect()
			->route('users.notes.index', $user);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function show(UserNote $note)
	{
		$this->authorize('view', $note);

		return view('user.note.show', ['note' => $note]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit(UserNote $note)
	{
		$this->authorize('update', $note);

		return view('user.note.edit', ['note' => $note]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreUserNote $request
	 * @param UserNote $note
	 * @return Response
	 */
	public function update(StoreUserNote $request, UserNote $note)
	{
		$this->authorize('update', $note);

		DB::beginTransaction();

		$note->fill($request->all());
		$note->save();

		DB::commit();

		return redirect()
			->route('users.notes.index', $note->create_user);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return UserNote $note
	 */
	public function destroy($id)
	{
		$note = UserNote::withTrashed()->findOrFail($id);

		if ($note->trashed()) {
			$this->authorize('restore', $note);
			$note->restore();
		} else {
			$this->authorize('delete', $note);
			$note->delete();
		}

		return $note;
	}
}
