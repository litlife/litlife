<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookFile;
use App\Events\BookFileHasBeenDownloaded;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Rules\ZipContainsBookFileRule;
use App\Rules\ZipRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Litlife\Url\Url;


class BookFileController extends Controller
{
	/**
	 * Список файлов у книги
	 *
	 * @param Book $book
	 * @return View
	 */
	public function index(Book $book)
	{
		$book_files = BookFile::where('book_id', $book->id)
			->get();

		return view('book_file.index', ['book' => $book, 'book_files' => $book_files]);
	}

	/**
	 * На проверке
	 *
	 * @return View
	 */
	public function onModeration()
	{
		$files = BookFile::sentOnReview()
			->with(['create_user', 'book'])
			->oldest()
			->simplePaginate();

		return view('book_file.on_moderation', ['files' => $files]);
	}

	/**
	 * форма добавления
	 *
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function create(Book $book)
	{
		$this->authorize('addFiles', $book);

		$fileExtensionsWhichCanExtractText = array_diff(config('litlife.book_allowed_file_extensions'), config('litlife.no_need_convert'));

		return view('book.file.create', compact('book', 'fileExtensionsWhichCanExtractText'));
	}

	/**
	 * Сохранение нового файла
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function store(Request $request, Book $book)
	{
		$this->authorize('addFiles', $book);

		$this->validate($request, [
			'file' => 'required|file|min:1|max:' . round(getMaxUploadNumberBytes() / 1000) . '|bail',
			'comment' => 'string|nullable',
			'number' => 'numeric|nullable'
		], [], __('book_file'));

		//$this->authorize('create', new BookFile);

		if ($request->file('file')->getMimeType() == 'application/zip')
			$this->validate($request, ['file' => ['bail', new ZipRule(), new ZipContainsBookFileRule()]], [], __('book_file'));
		else
			$this->validate($request, ['file' => 'book_file_extension',], [], __('book_file'));

		$file = new BookFile;
		$file->zip = true;
		$file->name = $request->file->getClientOriginalName();

		$extension = Url::fromString($request->file->getClientOriginalName())->getExtension();

		if (!empty($extension) and $extension != 'zip') {
			if (!in_array($extension, config('litlife.book_allowed_file_extensions'))) {
				return redirect()
					->back()
					->withErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
			}

			$file->extension = $extension;
		}

		$file->open($request->file('file')->getRealPath());
		$file->comment = $request->input('comment');
		$file->number = $request->input('number');
		if ($book->isPrivate()) {
			$file->statusPrivate();
		} else {
			// если пользователю можно добавлять книги без проверки
			if (auth()->user()->getPermission('book_file_add_without_check')) {
				// то книга проверерна
				$file->statusAccepted();
			} else {
				// проверяем принадлежит ли книга к которой прикрепляют файл текущему пользователю
				if ((!empty($file->book->create_user)) and ($file->book->create_user->id == auth()->id())) {
					// если да, то проверяем можно ли ему добавлять к своим книгам без проверки
					if (auth()->user()->getPermission('BookFileAddToSelfBookWithoutCheck'))
						// если да, то книга проверерна
						$file->statusAccepted();
					else
						// если нет, то добавляем на проверку
						$file->statusSentForReview();
				} else
					$file->statusSentForReview();
			}
		}
		$file->setRelation('book', $book);
		$book->files()->save($file);

		BookFile::flushCachedOnModerationCount();

		if (!$book->isHavePagesToRead()) {
			$file->sentParsePages();
		}

		activity()
			->performedOn($file)
			->log('created');

		$count = $book->files()
			->where('format', $file->extension)
			->where('id', '!=', $file->id)
			->count();

		if ($count < 1) {
			return redirect()
				->route('books.show', ['book' => $book])
				->with(['success' => __('book_file.uploaded_successfully')]);
		} else {
			return redirect()
				->route('books.files.edit', ['book' => $book, 'file' => $file])
				->withErrors(['comment' => __('validation.required', ['attribute' => __('book_file.comment')])]);
		}
	}

	/**
	 * Скачивание файла
	 *
	 * @param Book $book
	 * @param string $fileName
	 * @return Response
	 * @throws
	 */
	public function show(Book $book, $fileName)
	{
		$this->authorize('download', $book);
		/*
				abort(401);

				if ($book->isPrivate())
					$query->acceptedOrBelongsToAuthUser();
				else
					$query->acceptedAndSentForReview();
		*/
		$file = $book->files()
			->where("name", $fileName)
			->firstOrFail();

		if ($file->isPrivate()) {
			if (!auth()->check() or $file->create_user_id != auth()->id()) {
				abort(404);
				/*
				throw new AuthenticationException(
					__('user.unauthenticated_error_description', ['url' => route('invitation')])
				);
				*/
			}
		}

		if ($file->trashed())
			abort(404);

		if (!Storage::disk($file['storage'])->exists($file->dirname . '/' . $file->name))
			abort(404);

		event(new BookFileHasBeenDownloaded($file));

		if ($file->storage == 'private') {
			$url = $file->url;

			return response('')
				->header('X-Accel-Redirect', $url)
				->header('Content-Disposition', 'attachment; filename="' . $file->name . '"')
				->header('Content-Type', 'application/x-force-download');
		} else {
			$url = $file->getFullUrlWithScheme(Url::fromString(config('app.url'))->getScheme());

			return redirect()->to($file->url);
		}
	}

	/**
	 * Форма редактирования описания файла
	 *
	 * @param Book $book
	 * @param int $id
	 * @return View
	 * @throws
	 */
	public function edit(Book $book, $id)
	{
		$file = $book->files()->findOrFail($id);

		$this->authorize('update', $file);

		return view('book.file.edit', compact('book', 'file'));
	}

	/**
	 * Сохранение описания
	 *
	 * @param Request $request
	 * @param Book $book
	 * @param int $id
	 * @return Response
	 * @throws
	 */
	public function update(Request $request, Book $book, $id)
	{
		$file = $book->files()->findOrFail($id);

		$this->authorize('update', $file);

		$count = $book->files()
			->where('format', $file->extension)
			->where('id', '!=', $file->id)
			->count();

		$this->validate($request, [
			'comment' => $count ? 'string|required' : 'string|nullable',
			'number' => 'numeric|nullable'
		], [], __('book_file'));

		$file->fill($request->all());
		$file->update();

		activity()
			->performedOn($file)
			->log('updated');

		return redirect()
			->route('books.files.edit', ['book' => $book, 'file' => $file->id])
			->with(['success' => __('common.data_saved')]);
	}

	/**
	 * Удаление или восстановление
	 *
	 * @param Book $book
	 * @param  $id
	 * @return object
	 * @throws
	 */
	public function destroy(Book $book, $file)
	{
		$file = $book->files()->any()->findOrFail($file);

		if ($file->trashed()) {
			$this->authorize('restore', $file);

			$file->restore();

			activity()->performedOn($file)->log('restored');
		} else {
			$this->authorize('delete', $file);

			$file->delete();

			activity()->performedOn($file)->log('deleted');
		}

		return $file;
	}

	/**
	 * Одобрение файла книги
	 *
	 * @param BookFile $file
	 * @return Response
	 * @throws
	 */
	public function check($file)
	{
		$file = BookFile::any()->findOrFail($file);

		$this->authorize('approve', $file);

		$file->statusAccepted();
		$file->save();

		BookFile::flushCachedOnModerationCount();

		UpdateBookFilesCount::dispatch($file->book);

		activity()
			->performedOn($file)
			->log('approved');

		return redirect()
			->route('book_files.on_moderation')
			->with(['success' => __('book_file.approved')]);
	}

	/**
	 * Отклонить файл книги
	 *
	 * @param BookFile $file
	 * @return Response
	 * @throws
	 */
	public function decline($file)
	{
		$file = BookFile::any()->findOrFail($file);

		$this->authorize('decline', $file);

		$file->statusReject();
		$file->save();

		BookFile::flushCachedOnModerationCount();

		if (!empty($file->book))
			UpdateBookFilesCount::dispatch($file->book);

		activity()->performedOn($file)->log('declined');

		return redirect()
			->route('book_files.on_moderation')
			->with(['success' => __('book_file.declined')]);
	}

	/**
	 * Сделать из файла источник и создать страницы книги
	 *
	 * @param BookFile $file
	 * @return Response
	 * @throws
	 */
	public function setAsSourceAndMakePages(BookFile $file)
	{
		$this->authorize('set_source_and_make_pages', $file);

		DB::transaction(function () use ($file) {

			$file->sentParsePages();

			activity()
				->performedOn($file)
				->log('set_as_source');
		});

		return redirect()
			->route('books.show', ['book' => $file->book])
			->with(['success' => __('book_file.selected_as_source')]);
	}

	/**
	 * Сделать из файла источник и создать страницы книги
	 *
	 * @param BookFile $bookFile
	 * @return Response
	 * @throws
	 */
	public function activity_logs(BookFile $bookFile)
	{
		$activityLogs = $bookFile->activities()
			->latest()
			->simplePaginate();

		$activityLogs->load(['causer', 'subject' => function ($query) {
			$query->any();
		}]);

		return view('activity_log.index', compact('activityLogs'));
	}
}
