<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPhoto;
use App\User;
use App\UserPhoto;
use Illuminate\Http\Response;

class UserPhotoController extends Controller
{
	/**
	 * Сохранение
	 *
	 * @param StoreUserPhoto $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function index(User $user)
	{
		return redirect()
			->route('profile', ['user' => $user]);
	}

	/**
	 * Сохранение
	 *
	 * @param StoreUserPhoto $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function store(StoreUserPhoto $request, User $user)
	{
		$this->authorize('create_photo', $user);

		$photo = new UserPhoto;
		$photo->storage = config('filesystems.default');
		$photo->openImage($request->file('file')->getRealPath());
		$user->photos()->save($photo);

		$user->avatar_id = $photo->id;
		$user->save();

		return redirect()
			->route('users.edit', ['user' => $user])
			->with(['photo.success' => __('user_photo.upload_success')])
			->withInput();
	}

	/**
	 * Удаление
	 *
	 * @param User $user
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function destroy(User $user, $id)
	{
		$this->authorize('remove_photo', $user);

		$photo = $user->photos()->findOrFail($id);

		$photo->delete();

		return redirect()
			->route('users.edit', $user)
			->with(['photo.success' => __('user_photo.deleted')]);
	}
}
