<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Book;
use App\Jobs\Book\UpdateBookAttachmentsCount;
use Barryvdh\Debugbar\Facade;
use Debugbar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttachmentController extends Controller
{
	/**
	 * Список вложений в книге
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function index(Request $request, Book $book)
	{
		$this->authorize('view_section_list', $book);

		$attachments = $book->attachments()
			->with('book')
			->latest()
			->paginate(10);

		foreach ($attachments as $attachment) {
			$attachment->maxWidth = 150;
			$attachment->maxHeight = 100;
		}

		if ($request->input('CKEditor'))
			return view('attachment.index_ckeditor', compact('book', 'attachments'));
		else
			return view('attachment.index', compact('book', 'attachments'));
	}

	/**
	 * Добавление вложения из ckeditor а
	 *
	 * @param $request
	 * @param Book $book
	 * @return array, \Illuminate\View\View
	 * @throws
	 */
	public function storeFromCkeditor(Request $request, Book $book)
	{
		if (class_exists(Facade::class)) {
			Debugbar::disable();
		}

		$this->authorize('create_attachment', $book);

		$validator = Validator::make($request->all(), [
			'upload' => 'required|image|max:' . config('litlife.max_image_size') . '|mimes:' .
				implode(',', config('litlife.support_images_formats'))
		]);

		$file = $request->file('upload');

		if ($request->input('responseType') == 'json') {
			if ($validator->fails()) {
				$error['message'] = $validator->errors()->first();

				if (empty($file))
					return ['uploaded' => 0, 'fileName' => '', 'error' => $error];
				else
					return ['uploaded' => 0, 'fileName' => $file->getClientOriginalName(), 'error' => $error];
			}
		} else {
			if ($validator->fails()) {

				$message = $validator->errors()->first();

				return view('attachment.store_ckeditor', ['message' => $message]);
			}
		}

		$attachment = DB::transaction(function () use ($file, $book) {

			$attachment = new Attachment;
			$attachment->openImage($file->getRealPath());
			$attachment->storage = config('filesystems.default');
			$attachment->content_type = $file->getMimeType();
			$attachment->size = $file->getSize();
			$attachment->book_id = $book->id;
			$attachment->name = $file->getClientOriginalName();
			$attachment->type = 'image';
			$book->attachments()->save($attachment);

			$book->user_edited_at = now();
			$book->edit_user_id = auth()->id();
			$book->changed();
			$book->save();

			UpdateBookAttachmentsCount::dispatch($book);

			return $attachment;
		});

		if ($request->input('responseType') == 'json') {
			return ['uploaded' => 1, 'fileName' => $attachment->name, 'url' => $attachment->url];
		} else
			return view('attachment.store_ckeditor', ['url' => $attachment->url]);
	}

	/**
	 * Добавление вложения
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function store(Request $request, Book $book)
	{
		$this->validate($request, ['file' => 'required|image|max:' . config('litlife.max_image_size') . '|mimes:' .
			implode(',', config('litlife.support_images_formats'))], [], __('attachment'));

		$this->authorize('create_attachment', $book);

		DB::transaction(function () use ($request, $book) {

			$attachment = new Attachment;
			$attachment->openImage($request->file->getRealPath());
			$attachment->storage = config('filesystems.default');
			$attachment->content_type = $request->file->getMimeType();
			$attachment->size = $request->file->getSize();
			$attachment->book_id = $book->id;
			$attachment->name = $request->file->getClientOriginalName();
			$attachment->type = 'image';
			$attachment->save();

			if (!empty($request->input('setCover'))) {
				$book->cover()->associate($attachment);
				$book->save();

				activity()
					->performedOn($book)
					->withProperty('attachment_id', $attachment->id)
					->log('cover_add');
			}

			$book->user_edited_at = now();
			$book->edit_user_id = auth()->id();
			$book->changed();
			$book->save();

			UpdateBookAttachmentsCount::dispatch($book);
		});

		if (!empty($request->input('setCover'))) {
			return redirect()
				->route('books.edit', ['book' => $book])
				->with('success', __('attachment.uploaded'));
		} else {
			return redirect()
				->route('books.attachments.index', ['book' => $book])
				->with('success', __('attachment.uploaded'));
		}
	}

	/**
	 * Удаление вложения
	 *
	 * @param Book $book
	 * @param int $id
	 * @return Response, Attachment $attachment
	 * @throws
	 */
	public function delete(Book $book, $id)
	{
		$attachment = $book->attachments()
			->withTrashed()
			->findOrFail($id);

		if ($attachment->trashed()) {
			$this->authorize('restore', $attachment);
			$attachment->restore();
		} else {
			$this->authorize('delete', $attachment);
			$attachment->delete();
		}

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		UpdateBookAttachmentsCount::dispatch($book);

		if (request()->ajax())
			return $attachment;

		return back();
	}

	/**
	 * Сделать вложение изображение как обложку книги
	 *
	 * @param Book $book
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function setCover(Book $book, $id)
	{
		$attachment = $book->attachments()->findOrFail($id);

		$this->authorize('setAsCover', $attachment);

		$book->cover()->associate($attachment);
		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();



		activity()
			->performedOn($book)
			->log('set_cover');

		return redirect()
			->route('books.edit', $book)
			->with('success', __('attachment.selected_as_cover', ['name' => $attachment->name]));
	}

	/**
	 * Убрать обложку у книги
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function removeCover(Book $book)
	{
		$this->authorize('remove_cover', $book);

		if ($book->isForSale())
			return back()->withErrors(['file' => __('book.book_must_have_a_cover_for_sale')]);

		$book->cover()->dissociate();
		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		return redirect()
			->route('books.edit', $book)
			->with('success', __('attachment.cover_removed'));
	}
}
