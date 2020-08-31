<?php

namespace App\Http\Controllers;

use App\Author;
use App\AuthorPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorPhotoController extends Controller
{
	/**
	 * Редирект на страницу автора
	 *
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function index(Author $author)
	{
		return redirect()->route('authors.show', compact('author'));
	}

	/**
	 * Сохранение нового фото автора
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return Response
	 * @throws
	 */
	public function store(Request $request, Author $author)
	{
		$this->authorize('create_photo', $author);

		$this->validateWithBag('photo', $request,
			['file' => 'required|image|max:' . config('litlife.max_image_size') . '']);

		$photo = new AuthorPhoto;
		$photo->openImage($request->file('file')->getRealPath());
		$author->photos()->save($photo);

		$author->avatar()->associate($photo);
		$author->save();

		activity()
			->performedOn($author)
			->withProperty('photo_id', $photo->id)
			->log('photo_set');

		return back()->withInput();
	}

	/**
	 * Отображение фото автора в полном размере
	 *
	 * @param Author $author
	 * @param integer $id
	 * @return Response
	 * @throws
	 */
	public function show(Author $author, $id)
	{
		$photo = $author->photos()->findOrFail($id);

		return view('author.photo.show', compact('photo'));
	}

	/**
	 * Удаление фото автора
	 *
	 * @param Author $author
	 * @param integer $id
	 * @return Response
	 * @throws
	 */
	public function destroy(Author $author, $id)
	{
		$this->authorize('create_photo', $author);

		$photo = $author->photos()->findOrFail($id);

		$photo->delete();

		activity()
			->performedOn($author)
			->log('photo_remove');

		return back();
	}
}
