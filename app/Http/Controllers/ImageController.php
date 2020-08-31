<?php

namespace App\Http\Controllers;

use App\Image;
use Barryvdh\Debugbar\Facade;
use Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
	public function create()
	{
		$this->authorize('create', Image::class);

		if (request()->ajax())
			return view('image.create')->renderSections()['content'];
		else
			return view('image.create');
	}


	/**
	 * Добавление изображения
	 *
	 * @param Request $request
	 * @return mixed
	 * @throws
	 *
	 */

	public function store(Request $request)
	{
		$this->authorize('create', Image::class);

		if (class_exists(Facade::class)) {
			Debugbar::disable();
		}

		$validator = Validator::make($request->all(),
			['upload' =>
				'required|image|mimes:' .
				implode(',', config('litlife.support_images_formats'))
				. '|max:' . config('litlife.max_image_size')]);

		if ($validator->fails()) {
			if ($request->ajax()) {
				return ['errors' => $validator->errors()];
			} elseif ($request->input('responseType') == 'json') {
				$fileName = $request->file('upload')->getClientOriginalName();
				$uploaded = 0;
				$error['message'] = $validator->errors()->first();
				return compact('uploaded', 'error', 'fileName');
			} else {
				return view('image.store_ckeditor', ['message' => $validator->errors()->first()]);
			}
		}

		$image = new Image;
		$image->openImage($request->file('upload')->getRealPath());
		if (!empty($image_exists = auth()->user()->images()->sha256Hash($image->getImagick()->getImageSignature())->first()) and ($image_exists->exists())) {
			$image = $image_exists;
		} else {
			$image->storage = config('filesystems.default');
			$image->name = $request->file('upload')->getClientOriginalName();
			$image->save();
		}

		if ($request->ajax()) {
			return $image;
		} elseif ($request->input('responseType') == 'json') {

			$array = $image->toArray();
			$array['uploaded'] = '1';
			$array['fileName'] = $request->file('upload')->getClientOriginalName();
			$array['url'] = $image->url;

			return $array;
		} else {
			return view('image.store_ckeditor', ['url' => $image->url]);
		}
	}

	/**
	 * Удаление изображения
	 *
	 * @param int $id
	 * @throws
	 */

	public function destroy($id)
	{
		$this->authorize('delete', Image::class);
	}
}
