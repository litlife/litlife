<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\BookSimilarVote;
use App\BookStatus;
use App\BookVote;
use App\CollectedBook;
use App\Collection;
use App\Comment;
use App\Enums\AuthorEnum;
use App\Events\BookViewed;
use App\Genre;
use App\Http\Requests\StoreBook;
use App\Http\Requests\StoreBookCollected;
use App\Http\Requests\StoreDate;
use App\Http\Resources\BookCollection;
use App\Http\SearchResource\CollectionSearchResource;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Jobs\Book\BookAddKeywordsJob;
use App\Jobs\Book\BookDeleteKeywordsThatAreNotInTheListJob;
use App\Jobs\Book\BookGroupJob;
use App\Jobs\Book\BookPurchaseJob;
use App\Jobs\Book\UpdateBookAge;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Jobs\UpdateGenreBooksCount;
use App\Language;
use App\Manager;
use App\Notifications\BookDeletedNotification;
use App\Notifications\BookPublishedNotification;
use App\Notifications\BookRemovedFromPublicationNotification;
use App\Notifications\BookRemovedFromSaleNotification;
use App\Rules\ZipContainsBookFileRule;
use App\Rules\ZipRule;
use App\Scopes\CheckedScope;
use App\Section;
use App\Sequence;
use App\UserBook;
use App\UserIncomingPayment;
use App\UserPaymentTransaction;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Carbon\Carbon;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Url\Url;

class BookController extends Controller
{
	/**
	 * Форма создание новой книги
	 *
	 * @return View
	 * @throws
	 */
	public function create()
	{
		/*
		$languages = Language::all();

		$book = new Book;
		$book->title = 'Новая книга ' . Date::now()->format('j F Y H:i:s');
		$book->parse_status = 'ok';
		$book->save();

		$genres = Genre::select('id', 'name', 'book_count')
			->orderBy('book_count', 'desc')
			->get();
		*/
		$this->authorize('create', Book::class);

		$fileExtensionsWhichCanExtractText = array_diff(config('litlife.book_allowed_file_extensions'), config('litlife.no_need_convert'));

		return view('book.create', compact('fileExtensionsWhichCanExtractText'));
	}

	/**
	 * Сохранение новой книги
	 *
	 * @param Request $request
	 * @return View
	 * @return Response
	 * @throws
	 */
	public function store(Request $request)
	{
		$this->authorize('create', Book::class);

		$this->validate($request, [
			//'file' => 'required_if:title,null|file|max:10000|book_file_extension',
			'file' => 'required_if:title,null|file|min:1|max:' . round(getMaxUploadNumberBytes() / 1000) . '',
			'title' => 'required_if:file,null|string'
		], [], __('book'));

		if ($request->file('file')) {
			if ($request->file('file')->getMimeType() == 'application/zip')
				$this->validate($request, ['file' => ['bail', new ZipRule, new ZipContainsBookFileRule]], [], __('book'));
			else
				$this->validate($request, ['file' => 'book_file_extension'], [], __('book'));

			$filename = trim(Url::fromString($request->file->getClientOriginalName())->getFilename());
			$filename = empty($filename) ? 'Title' : $filename;

			$book = new Book;
			$book->title = pathinfo($filename, PATHINFO_FILENAME);
			$book->updateTitleAuthorsHelper();
			$book->save();

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
			$file->source = true;

			$book->files()->save($file);

			// если расширение файла присутствует в списке файлов которые не нужно обрабатывать
			if ($file->canParsed()) {
				// нужно распарсить файл
				$book->parse->autoAssociateAuthUser();
				$book->parse->wait();
			}

			$book->push();

			// добавляем книгу в личную библиотеку
			//UserBook::create(['book_id' => $book->id]);

			activity()
				->performedOn($book)
				->log('created');

			return redirect()
				->route('books.create.description', $book)
				->with(['success' => __('book_file.upload_success')]);

		} elseif ($request->input('title')) {
			$book = new Book;
			$book->title = $request->input('title');
			//$book->year_writing = Carbon::now()->year;
			$book->updateTitleAuthorsHelper();
			$book->save();

			// добавляем книгу в личную библиотеку
			//UserBook::create(['book_id' => $book->id]);

			activity()
				->performedOn($book)
				->log('created');

			return redirect()
				->route('books.create.description', $book);
		} else {
			return back()->withErrors([__('common.error')]);
		}
	}

	/**
	 * Страница ожидания обработки книги
	 *
	 * @param Book $book
	 * @return View
	 */
	public function description(Request $request, Book $book)
	{
		$this->authorize('view', $book);

		if ($book->trashed())
			return redirect()
				->route('books.create');

		if ($book->parse->isFailed()) {
			return view('book.create.failed_processing', ['book' => $book]);
		} elseif (!$book->parse->isSucceed()) {
			return view('book.create.processing', ['book' => $book]);
		} else {

			$this->authorize('update', $book);

			js_put('book', $book);

			$languages = Language::all();

			$genres = Genre::select(['id', 'name', 'book_count'])
				->notMain()
				->orderBy('book_count', 'desc')
				->get();

			if (isset($book->cover)) {
				$book->cover->maxWidth = 200;
				$book->cover->maxHeight = 200;
			}

			$view = view('book.create.edit', [
				'book' => $book,
				'languages' => $languages,
				'genres' => $genres,
				'redirectSuccessUrl' => route('books.create.complete', $book),
				'cantEditSiLpPublishFields' => false
			]);

			if (!empty($request->session()->get('errors'))) {
				return $view;
			} else {
				$book->load([
					'genres',
					'writers',
					'sequences',
					'book_keywords'
				]);

				$errors = new MessageBag();

				$validator = Validator::make($book->toArray(), (new StoreBook)->rules(), [], __('book'));

				return $view->withErrors($validator->errors());
			}
		}
	}

	public function createComplete(Book $book)
	{
		$this->authorize('view', $book);

		if ($book->trashed())
			return redirect()
				->route('books.create');

		return view('book.create.complete', ['book' => $book]);
	}

	/**
	 * Страница книги
	 *
	 * @param Book $book
	 * @return View
	 */
	public function show(Book $book)
	{
		if ($book->isInGroup() and $book->isNotMainInGroup() and !empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		if ($book->isInGroup())
			$query = Comment::whereIn('commentable_id', array_merge([$mainBook->id], $mainBook->groupedBooks()->get()->pluck('id')->toArray()))
				->bookType();
		else
			$query = $book->comments();

		$top_comments = (clone $query)
			->where('origin_commentable_id', $book->id)
			->roots()
			->latest()
			->limit(33)
			->get()
			->sortByDesc('vote')
			->where('vote', '>', '1')
			->take(2);

		$top_comments->load([
			"create_user.avatar",
			'originCommentable.authors.managers',
			'create_user.latest_user_achievements.achievement.image',
			'create_user.groups',
			"userBookVote",
			'votes' => function ($query) {
				$query->where("create_user_id", auth()->id());
			}
		]);

		$top_comments_ids = $top_comments->pluck('id')->toArray();

		$comments = (clone $query)
			// исключаем комментарии, которые уже есть в топе
			->when(!empty($top_comments_ids), function ($query) use ($top_comments_ids) {
				return $query->whereNotIn('id', $top_comments_ids);
			})
			->acceptedAndSentForReviewOrBelongsToAuthUser()
			->roots()
			->orderByOriginFirstAndLatest($book)
			->paginate(config('litlife.comments_on_page_count'));

		$comments->load([
			"create_user.avatar",
			'originCommentable.authors.managers',
			'create_user.latest_user_achievements.achievement.image',
			'create_user.groups',
			"userBookVote",
			'votes' => function ($query) {
				$query->where("create_user_id", auth()->id());
			}]);

		// комментарии по умолчанию свернуты, но при переходе к комментарию нужно открыть дерево комментариев и загрузить его
		if (request()->input('comment')) {
			$comment = $book->commentsOrigin()->find(intval(request()->input('comment')));

			if (!empty($comment)) {
				if ($comment->level > 0) {
					$comment = $comment->root;
				}

				$comment->load(['votes' => function ($query) {
					$query->where("create_user_id", auth()->id());
				}]);

				$descendants = $query->with("create_user.avatar")
					->descendants($comment->id)
					->oldest()
					->get();

				$descendants->load(['votes' => function ($query) {
					$query->where("create_user_id", auth()->id());
				}]);
			}
		}

		if (request()->ajax())
			return view('book.show.comments', compact(
				'book', 'top_comments', 'comments'
			));

		//

		if ($book->isSentForReview()) {
			$book->load([
				'authors' => function ($query) {
					$query->acceptedAndSentForReviewOrBelongsToUser(auth()->user());
				},
				'sequences' => function ($query) {
					$query->acceptedAndSentForReviewOrBelongsToUser(auth()->user());
				}
			]);

			$mainBook->load([
				'book_keywords' => function ($query) {
					$query->acceptedAndSentForReviewOrBelongsToUser(auth()->user())
						->whereHas('keyword')
						->with('keyword');
				}
			]);
		}

		$book->loadMissing(['authors.managers']);

		$book->setRelation('writers', $book->getAuthorsWithType(AuthorEnum::Writer));
		$book->setRelation('editors', $book->getAuthorsWithType(AuthorEnum::Editor));
		$book->setRelation('illustrators', $book->getAuthorsWithType(AuthorEnum::Illustrator));
		$book->setRelation('translators', $book->getAuthorsWithType(AuthorEnum::Translator));
		$book->setRelation('compilers', $book->getAuthorsWithType(AuthorEnum::Compiler));

		$book->loadMissing([
			'files' => function ($query) use ($book) {
				$query->orderBy("source", 'asc');

				if ($book->isPrivate()) {
					$query->withoutGlobalScope(CheckedScope::class);
				} elseif ($book->isSentForReview() or $book->isAccepted()) {
					if (auth()->check() and auth()->user()->can('view_on_moderation', BookFile::class))
						$query->acceptedAndSentForReviewOrBelongsToUser(auth()->user());
					else
						$query->acceptedOrBelongsToUser(auth()->user());
				}
			},
			'files.create_user',
			'remembered_pages' => function ($query) {
				$query->where("user_id", auth()->id());
			}]);

		foreach ($book->files as $file)
			$file->setRelation('book', $book);

		SharedData::put(['book_id' => $book->id]);

		$books_similar = $book->similars()
			->havingRaw('SUM("vote") > 0')
			->orderBy("sum", "desc")
			->limit(10)
			->get();

		$books_similar->load(['similar_vote' => function ($query) {
			$query->where("create_user_id", auth()->id());
		}]);

		foreach ($books_similar as $book_similar)
			$book_similar->setRelation('writers', $book_similar->getAuthorsWithType(AuthorEnum::Writer));

		$user_book_vote = $mainBook->votes()
			->where('create_user_id', auth()->id())->first();

		$auth_user_like = $mainBook->likes()
			->where('create_user_id', auth()->id())->first();

		$user_read_status = $mainBook->users_read_statuses()
			->where('user_id', auth()->id())->first();

		$auth_user_library = $book->library_users()
			->where('user_id', auth()->id())->first();

		if (auth()->check()) {
			$remembered_page = $book->remembered_pages->first();
		}

		if (auth()->id() != 50000) {
			if (!$book->isHaveAccess())
				return response()
					->view('book.show.access_denied', get_defined_vars(), 403);
		}

		$genre_ids = $book->genres->pluck('id')->toArray();

		$limit = (10 - $books_similar->count());

		if ($limit < 1) $limit = 0;

		if (!empty($genre_ids)) {
			/*
					 $rand_books = Book::genre($genre_ids)
						->where(function ($query) {
						   for ($a = 0; $a < 6; $a++) {
							  $rand = rand(0, Cache::get('books_count'));
							  $query->orWhereBetween('id', [($rand - 100), $rand]);
						   }
						})
						->notConnected()
						->with('writers.managers.user', 'cover')
						->limit($limit)
						->get()
						->shuffle();
			*/
			$bookOnSale = Book::genre($genre_ids)
				->select('books.*')
				->paid()
				->where('id', '!=', $book->id)
				->orderByRatingDayDesc()
				->limit(4)
				->get();

			$rand_books = Book::genre($genre_ids)
				->where(function ($query) {
					for ($a = 0; $a < 6; $a++) {
						$rand = rand(0, Cache::get('books_count'));
						$query->orWhereBetween('id', [($rand - 100), $rand]);
					}
				})
				->orderByRatingDesc()
				->onlyReadAccess()
				->notConnected()
				->limit($limit)
				->get();

			$rand_books = $bookOnSale->merge($rand_books);

			$rand_books->load(['writers.managers.user', 'cover']);
		}

		if ($book->isPagesNewFormat()) {
			$first_section = Section::scoped(['book_id' => $book->id, 'type' => 'section'])
				->accepted()
				->orderBy('_lft', 'asc')
				->first();
		}

		event(new BookViewed($mainBook));

		SEOMeta::addKeyword($book->book_keywords->pluck('keyword.text')->toArray());

		$title = $book->title . ' - ' . implode(', ', $book->writers()->get()->pluck('name')->toArray());

		OpenGraph::setTitle($title)
			->setType('book')
			->setBook([
				'author' => $book->writers->map(function ($author, $key) {
					return route('authors.show', $author);
				})->toArray(),
				'isbn' => $book->pi_isbn,
				'release_date' => $book->pi_year ?? $book->year_writing
			]);

		TwitterCard::setTitle($title);

		$description = $book->getSEODescription();

		TwitterCard::setDescription($description);
		OpenGraph::setDescription($description);
		SEOMeta::setDescription($description);

		if (!empty($book->cover)) {
			$cover_url = Url::fromString($book->cover->fullUrlMaxSize(900, 900));

			OpenGraph::addImage($cover_url->withScheme('http'),
				[
					'secure_url' => $cover_url->withScheme('https'),
					'type' => $book->cover->content_type,
					'alt' => $book->title
				]);

			TwitterCard::setImage($book->cover->fullUrlMaxSize(900, 900));
		}

		if (auth()->check()) {
			if (!empty($user_read_status) and $user_read_status->status == 'readed') {
				if (empty($user_book_vote) or empty($user_book_vote->vote)) {
					if (!$book->isUserVerifiedAuthorOfBook(auth()->user())) {
						$ask_user_to_rate_the_book = true;
					}
				}
			}
		}

		$collectionsCount = $book->collections()
			->acceptedOrBelongsToAuthUser()
			->count();

		if (!empty($book->ti_lb))
			OpenGraph::addProperty('locale', strtolower($book->ti_lb) . '_' . strtoupper($book->ti_lb));

		if ($book->trashed()) {

			if (auth()->check() and auth()->user()->can('see_deleted', $book)) {
				$show_even_if_trashed = 1;

			} else if (auth()->check() and $purchase = $book->purchases->where('buyer_user_id', auth()->user()->id)->first()) {

			} else
				return response()->view('book.show.trashed', get_defined_vars(), 404);
		}

		return response()->view('book.show.show', get_defined_vars());
	}

	/**
	 * Форма редактирования
	 *
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function edit(Book $book)
	{
		$this->authorize('update', $book);

		js_put('book', $book);

		$languages = Language::all();

		$genres = Genre::select('id', 'name', 'book_count')
			->notMain()
			->orderBy('book_count', 'desc')
			->get();

		if (isset($book->cover)) {
			$book->cover->maxWidth = 200;
			$book->cover->maxHeight = 200;
		}

		if (auth()->user()->cant('editSiLpPublishFields', $book))
			$cantEditSiLpPublishFields = true;
		else
			$cantEditSiLpPublishFields = false;

		return view('book.edit', [
			'book' => $book,
			'languages' => $languages,
			'genres' => $genres,
			'cantEditSiLpPublishFields' => $cantEditSiLpPublishFields
		]);
	}


	public function edit_sequence_item($id)
	{
		$sequence = Sequence::findOrFail($id);

		return view('book.edit.sequence_item', compact('sequence'));
	}

	/**
	 * Сохранение отредатктированной книги
	 *
	 * @param StoreBook $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function update(StoreBook $request, Book $book)
	{
		$this->authorize('update', $book);

		if (!empty($request->is_si) and is_array($request->translators) and count($request->translators) > 0) {
			return redirect()
				->route('books.edit', $book)
				->withInput($request->all())
				->withErrors(['is_si' => __('book.you_cant_set_the_si_label_if_the_translator_is_specified')]);
		}

		$annotation = new Section;

		$annotationLength = $annotation->getCharacterCountInText($request->annotation);

		if ($annotationLength > config('litlife.max_section_characters_count')) {
			return redirect()
				->route('books.edit', $book)
				->withErrors(['content' => __('validation.max.string', [
					'max' => config('litlife.max_annotation_characters_count'),
					'attribute' => __('book.annotation')
				])])
				->withInput($request->all());
		}

		if ($book->isForSale()) {
			if ($annotationLength < config('litlife.min_annotation_characters_count_for_sale'))
				return redirect()
					->route('books.edit', $book)
					->withInput($request->all())
					->withErrors(['annotation' => __('book.annotation_must_contain_at_least_characters_for_sale', [
						'characters_count' => config('litlife.min_annotation_characters_count_for_sale')
					])]);

			if (!in_array($request->ready_status, ['complete', 'not_complete_but_still_writing']))
				return redirect()
					->route('books.edit', $book)
					->withInput($request->all())
					->withErrors(['ready_status' => __('book.if_the_book_is_on_sale_the_text_of_the_book_may_have_the_status_only_finished_or_not_finished_and_is_still_being_written')]);
		}

		$book->load([
			'authors',
			'genres' => function ($query) {
				$query->notMain();
			},
			'translators',
			'sequences']);

		$old_book = clone $book;

		DB::beginTransaction();

		$except = [];

		if ($book->isForSale()) {
			$except[] = 'is_si';
			$except[] = 'is_lp';
		}

		if (auth()->user()->cant('editFieldOfPublicDomain', $book)) {
			$except[] = 'is_public';
			$except[] = 'year_public';
		}

		if (auth()->user()->cant('editSiLpPublishFields', $book)) {
			$except[] = 'is_si';
			$except[] = 'is_lp';
			$except[] = 'pi_pub';
			$except[] = 'pi_city';
			$except[] = 'pi_year';
			$except[] = 'pi_isbn';
		}

		$book->fill($request->except($except));

		$book->genres()->sync(empty($request->genres) ? [] :
			Genre::whereIn('id', $request->genres)
				->notMain()
				->orderByField('id', $request->genres)->get()
				->mapWithKeys(function ($item, $order) {
					return [$item->id => ['order' => $order]];
				})->toArray());

		foreach (AuthorEnum::asArray() as $key => $type) {
			$relation = mb_strtolower($key . 's');

			$array = empty($request->$relation) ? [] :
				Author::whereIn('id', $request->$relation)
					->orderByField('id', $request->$relation)->get()
					->mapWithKeys(function ($item, $order) use ($type) {
						return [$item->id => ['order' => $order]];
					})->
					toArray();

			if (auth()->user()->cant('changeWritersField', $book)) {
				if ($relation == 'writers') {
					$compareArray1 = array_keys($array);

					$compareArray2 = array_keys($book->$relation()->get()
						->mapWithKeys(function ($item, $order) use ($type) {
							return [$item->id => ['order' => $order]];
						})->toArray());

					sort($compareArray1);
					sort($compareArray2);

					if ($compareArray1 != $compareArray2)
						return redirect()
							->route('books.edit', $book)
							->withInput($request->all())
							->withErrors(['writers' => __('book.you_cannot_change_the_data_in_the_writers_field_if_the_book_is_on_sale')]);
				}
			}

			$book->$relation()->sync($array);
		}

		$book->sequences()->detach();

		if (!empty($request->sequences)) {
			$order = 0;
			foreach ($request->sequences as $sequence) {
				$book->sequences()->syncWithoutDetaching([
					$sequence['id'] => [
						'number' => intval($sequence['number']),
						'order' => intval($order)
					]
				]);
				$order++;
			}
		}

		if (auth()->user()->can('addKeywords', $book)) {
			if (is_array($request->keywords)) {
				BookAddKeywordsJob::dispatch($book, $request->keywords);
				BookDeleteKeywordsThatAreNotInTheListJob::dispatch($book, $request->keywords);
			}
		}

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();

		if (!empty($request->illustrators))
			$book->images_exists = true;

		if ($book->isAccepted() and !auth()->user()->getPermission('edit_other_user_book')) {
			$newAuthors = $book->authors()->get()->diff($old_book->authors);

			$newAuthors->load('managers');

			foreach ($newAuthors as $author) {
				if ($author->managers->where('user_id', auth()->id())->count() < 1) {
					$book->statusSentForReview();
				}
			}
		}
		$book->save();

		$annotation = Section::scoped(['book_id' => $book->id, 'type' => 'annotation'])
			->first();

		if ($request->annotation) {

			if (empty($annotation)) {
				$annotation = new Section;
				$annotation->inner_id = 0;
				$annotation->book_id = $book->id;
				$annotation->title = 'Annotation';
				$annotation->type = 'annotation';
				$annotation->content = $request->annotation;
			} else {
				$annotation->content = $request->annotation;
			}
			$annotation->save();

		} elseif (!empty($annotation)) {
			$annotation->delete();
		}

		$book->refresh()->load('authors', 'genres', 'translators', 'sequences');

		if ($book->isAccepted()) {
			foreach ($book->authors as $author) {
				if (!$author->isAccepted()) {
					$author->statusAccepted();
					$author->save();
				}
			}
			foreach ($book->sequences as $sequence) {
				if (!$sequence->isAccepted()) {
					$sequence->statusAccepted();
					$sequence->save();
				}
			}
		}

		// ищем разницу между какие были авторы, серии и тд и каких нет и обновляем у них количество книг
		$old_book->genres->diff($book->genres)->merge($book->genres->diff($old_book->genres))->each(function ($genre) {
			UpdateGenreBooksCount::dispatch($genre);
		});

		foreach (AuthorEnum::asArray() as $key => $type) {
			$old_book->getAuthorsWithType($type)
				->diff($book->getAuthorsWithType($type))
				->merge($book->getAuthorsWithType($type)->diff($old_book->getAuthorsWithType($type)))
				->each(function ($author) {
					$author->ratingChanged();
					UpdateAuthorBooksCount::dispatch($author);
				});
		}

		$old_book->sequences->diff($book->sequences)->merge($book->sequences->diff($old_book->sequences))->each(function ($sequence) {
			UpdateSequenceBooksCount::dispatch($sequence);
		});

		UpdateBookAge::dispatch($book);

		$book->updateTitleAuthorsHelper();
		$book->changed();
		$book->save();

		activity()->performedOn($book)
			->log('updated');

		DB::commit();

		if (isset($request->redirect_success_url)) {
			return redirect()
				->away($request->redirect_success_url);
		} else {
			return redirect()
				->route('books.edit', $book)
				->with('success', __('common.data_saved'));
		}
	}

	/**
	 * Вывод формы для заполнения причины удаления книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function deleteForm(Book $book)
	{
		$this->authorize('delete', $book);

		return view('book.delete', ['book' => $book]);
	}

	/**
	 * Удаление книги
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function delete(Request $request, Book $book)
	{
		if (!$book->trashed()) {
			if ($book->isMainInGroup())
				return redirect()
					->route('books.show', $book)
					->withErrors([__('book.you_cannot_delete_a_book_while_it_is_the_main_edition')]);
		}

		$this->authorize('delete', $book);

		DB::transaction(function () use ($book) {
			$book->deletedByUser()->associate(auth()->user());
			$book->delete();
		});

		activity()->performedOn($book)
			->withProperty('reason', $request->reason_for_deleting)
			->log('deleted');

		if (!auth()->user()->is($book->create_user))
			$book->create_user->notify(new BookDeletedNotification($book, $request->reason_for_deleting));

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.deleted')]);
	}

	/**
	 * Восстановление книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function restore(Book $book)
	{
		$this->authorize('restore', $book);

		DB::transaction(function () use ($book) {
			$book->deletedByUser()->associate(auth()->user());
			$book->restore();
		});

		activity()->performedOn($book)
			->log('restored');

		return back();
	}

	/**
	 * Оценка книги
	 *
	 * @param Book $book , int $vote
	 * @return mixed
	 * @throws
	 */
	public function vote(Book $book, $vote)
	{
		$this->authorize('vote', $book);

		$user = auth()->user();

		DB::beginTransaction();

		if (!empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		$book_vote = $mainBook->votes()
			->where('create_user_id', $user->id)
			->withTrashed()
			->first();

		if (empty($book_vote)) {
			$book_vote = new BookVote;
			$book_vote->create_user_id = $user->id;
			$book_vote->book_id = $mainBook->id;
		}

		$book_vote->origin_book_id = $book->id;
		$book_vote->vote = $vote;
		$book_vote->ip = request()->ip();
		$book_vote->user_updated_at = now();

		if ($book_vote->trashed()) {
			$book_vote->restore();
		} else {
			ignoreDuplicateException(function () use ($book_vote) {
				$book_vote->save();
			});
		}

		DB::commit();

		if (request()->ajax())
			return $book_vote;
		else
			return redirect()
				->route('books.show', $book)
				->with(['success' => __('You have successfully set a rating: :description', [
					'description' => __('book.vote_descriptions.' . $book_vote->vote)
				])]);
	}

	/**
	 * Удаление оценки
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */

	public function voteRemove(Book $book)
	{
		$this->authorize('vote_remove', $book);

		$book_vote = $book->votes()
			->where('create_user_id', auth()->id())
			->first();

		if (!empty($book_vote))
			$book_vote->delete();

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('Rating was successfully deleted')]);
	}

	/**
	 * Добавить похожую книгу
	 *
	 * @param Request $request , Book $book
	 * @return Response
	 * @throws
	 */

	public function addSimilarBook(Request $request, Book $book)
	{
		$this->authorize('add_similar_book', $book);

		$this->validateWithBag('similar_vote', $request,
			['book_id' => 'required|numeric|exists:books,id|not_in:' . $book->id . ''], [], trans('book_similar_vote'));

		$otherBook = Book::findOrFail($request->book_id);

		$bookSimilarVote = $otherBook->similar_vote()
			->updateOrCreate(
				['book_id' => $book->id, 'create_user_id' => auth()->id()],
				['vote' => 1]
			);

		return back();
	}

	/**
	 * Голосование за похожую книгу
	 *
	 * @param Book $book , Book $otherBook, int $vote
	 * @return BookSimilarVote $bookSimilarVote
	 * @throws
	 */

	public function voteForSimilar(Book $book, Book $otherBook, $vote)
	{
		$this->authorize('add_similar_book', $book);

		if ($book->id == $otherBook->id)
			abort(422);

		$bookSimilarVote = BookSimilarVote::where('book_id', $book->id)
			->where('other_book_id', $otherBook->id)
			->where('create_user_id', auth()->id())
			->first();

		if (empty($bookSimilarVote)) {
			$bookSimilarVote = new BookSimilarVote;
			$bookSimilarVote->book_id = $book->id;
			$bookSimilarVote->other_book_id = $otherBook->id;
			$bookSimilarVote->vote = $vote;
		} else {

			if ((($bookSimilarVote->vote > 0) and ($vote > 0)) or (($bookSimilarVote->vote < 0) and ($vote < 0))) {
				$bookSimilarVote->vote = 0;
			} else {
				$bookSimilarVote->vote = $vote;
			}
		}
		$bookSimilarVote->save();

		return $bookSimilarVote;
	}

	/**
	 * Добавление или удаление книги в избранное
	 *
	 * @param Book $book
	 * @return array
	 * @throws
	 */
	public function toggle_my_library(Book $book)
	{
		$user = auth()->user();

		$user->flushCachedFavoriteBooksWithUpdatesCount();

		$user_book_pivot = $book->library_users()
			->where('user_id', $user->id)
			->first();

		if (empty($user_book_pivot)) {
			/*
			$user_book_pivot =
				->where('user_id', $user->id)
				->updateOrCreate([]);
			*/
			$user_book_pivot = new UserBook(['user_id' => $user->id]);
			$book->library_users()->save($user_book_pivot);
		} else {
			$user_book_pivot->delete();
			unset($user_book_pivot);
		}

		$book->refresh();

		if (!empty($user_book_pivot))
			return [
				'result' => 'attached',
				'added_to_favorites_count' => $book->added_to_favorites_count
			];
		else
			return [
				'result' => 'detached',
				'added_to_favorites_count' => $book->added_to_favorites_count
			];
	}

	/**
	 * Установка статуса прочтения автора
	 *
	 * @param Book $book
	 * @param string $status
	 * @return BookStatus $status
	 * @throws
	 */
	public function read_status(Book $book, $status)
	{
		DB::beginTransaction();

		if (!empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		$status = ignoreDuplicateException(function () use ($mainBook, $book, $status) {
			return BookStatus::updateOrCreate(
				[
					'user_id' => auth()->id(),
					'book_id' => $mainBook->id
				],
				[
					'status' => $status,
					'user_updated_at' => now(),
					'origin_book_id' => $book->id
				]
			);
		});

		DB::commit();

		if (request()->ajax())
			return $status;
		else
			return redirect()->route('books.show', $book);
	}

	/**
	 * Публикация книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function publish(Book $book)
	{
		$this->authorize('publish', $book);

		DB::beginTransaction();

		$book->load([
			'genres',
			'writers',
			'sequences',
			'book_keywords'
		]);

		$validator = Validator::make($book->toArray(), (new StoreBook)->rules(), [], __('book'));

		if ($validator->fails())
			return redirect()
				->route('books.edit', $book)
				->withErrors($validator)
				->with(['try_publish' => true])
				->withInput();

		$book->statusSentForReview();

		if (auth()->user()->getPermission('add_book_without_check') or auth()->user()->getPermission('check_books')) {
			$book->statusAccepted();
		}

		$book->authors->loadMissing('managers');

		$manager = $book->authors->first(function ($author, $key) {
			if ($manager = $author->managers->where('user_id', auth()->id())->first()) {
				if ($manager->isAccepted() and $manager->character == 'author') {
					return $manager;
				}
			}
		});

		if (!empty($manager))
			$book->statusAccepted();
		else {
			if (!$book->is_lp) {
				$book->readAccessDisable();
				$book->downloadAccessDisable();
			}
		}

		if (auth()->user()->isOnModeration())
			$book->statusSentForReview();

		if ($book->sections()->chapter()->accepted()->count() < 1)
			$book->readAccessDisable();

		if ($book->files()->count() < 1)
			$book->downloadAccessDisable();

		$book->save();

		if ($book->isAccepted()) {

			$book->publish();

			UpdateBookFilesCount::dispatch($book);
			Book::flushCachedOnModerationCount();
			Book::cachedCountRefresh();
			BookFile::flushCachedOnModerationCount();

			activity()->performedOn($book)->log('make_accepted');

			if (!empty($book->create_user) and !$book->isAuthUserCreator())
				$book->create_user->notify(new BookPublishedNotification($book));

			DB::commit();

			if ($book->isForSale()) {
				return redirect()
					->route('books.show', $book)
					->with('success', __('book.book_has_been_published_and_is_now_on_sale'));
			} else {
				return redirect()
					->route('books.show', $book)
					->with('success', __('book.published'));
			}

		} else {
			// добавляет еще непроверенных авторов этой книги на проверку
			foreach ($book->authors as $author) {
				if ($author->isPrivate()) {
					$author->statusSentForReview();
					$author->save();
				}

				foreach ($author->managers as $manager) {
					if ($manager->isPrivate()) {
						$manager->statusSentForReview();
						$manager->save();
					}
				}
			}

			// добавляет еще непроверенные серии из этой книги на проверку
			foreach ($book->sequences as $sequence) {
				if ($sequence->isPrivate()) {
					$sequence->statusSentForReview();
					$sequence->save();
				}
			}

			// добавляет ключевые слова которые не проверенны
			foreach ($book->book_keywords()->private()->get() as $book_keyword) {
				$book_keyword->statusSentForReview();
				$book_keyword->save();
			}

			foreach ($book->files()->private()->get() as $file) {

				if ($file->isAutoCreated())
					$file->statusAccepted();
				else
					$file->statusSentForReview();

				$file->save();
			}

			$book->genres()->get()->each(function ($genre) {
				UpdateGenreBooksCount::dispatch($genre);
			});

			$book->authors->each(function ($author) {
				$author->ratingChanged();
				UpdateAuthorBooksCount::dispatch($author);
			});

			$book->sequences->each(function ($sequence) {
				UpdateSequenceBooksCount::dispatch($sequence);
			});

			UpdateBookFilesCount::dispatch($book);
			Book::flushCachedOnModerationCount();
			BookFile::flushCachedOnModerationCount();

			activity()->performedOn($book)
				->log('add_for_review');

			DB::commit();

			return redirect()
				->route('books.show', $book)
				->with('success', __('book.added_for_check'));
		}
	}

	/**
	 * Вывод формы для заполнения причины или прочих опций при снятии книги с публикации
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function addToPrivateForm(Book $book)
	{
		$this->authorize('addToPrivate', $book);

		return view('book.add_to_private', ['book' => $book]);
	}

	/**
	 * Отправка книги в личную бибилиотеку тому кто ее добавил
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function addToPrivate(Request $request, Book $book)
	{
		$this->authorize('addToPrivate', $book);

		$this->validate($request, ['reason_for_removal_from_publication' => 'string|nullable'], [], __('book'));

		DB::transaction(function () use ($request, $book) {
			// убирает книгу с проверки
			$book->statusPrivate();
			$book->save();

			// удаляет авторов на проверке с проверки
			foreach ($book->authors()->sentOnReview()->get() as $author) {
				$author->statusPrivate();
				$author->save();

				// если есть не проверенные верификации авторов, то убираем их с проверки
				foreach ($author->managers as $manager) {
					if ($manager->isSentForReview()) {
						$manager->statusPrivate();
						$manager->save();

						$flushManagerCounter = true;
					}
				}
			}

			// удаляет серии на проверке с проверки
			foreach ($book->sequences()->sentOnReview()->get() as $sequence) {
				$sequence->statusPrivate();
				$sequence->save();
			}

			foreach ($book->book_keywords()->get() as $book_keyword) {
				$book_keyword->statusPrivate();
				$book_keyword->save();
			}

			foreach ($book->files()->get() as $file) {
				$file->statusPrivate();
				$file->save();
			}

			$book->genres->each(function ($genre) {
				UpdateGenreBooksCount::dispatch($genre);
			});

			$book->authors->each(function ($author) {
				UpdateAuthorBooksCount::dispatch($author);
				$author->flushUsersAddedToFavoritesNewBooksCount();
			});

			$book->sequences->each(function ($sequence) {
				UpdateSequenceBooksCount::dispatch($sequence);
			});

			UpdateBookFilesCount::dispatch($book);
			Book::flushCachedOnModerationCount();
			BookFile::flushCachedOnModerationCount();

			if (!empty($flushManagerCounter))
				Manager::flushCachedOnModerationCount();

			activity()
				->performedOn($book)
				->withProperties([
					'reason' => $request->reason_for_removal_from_publication,
				])
				->log('add_to_private');

			if (!auth()->user()->is($book->create_user))
				$book->create_user->notify(new BookRemovedFromPublicationNotification($book, $request->reason_for_removal_from_publication));
		});

		return redirect()
			->route('books.show', $book)
			->with('success', __('book.rejected_and_sended_to_private'));
	}

	/**
	 * Добавить книгу в общую библиотеку
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function makeAccepted(Book $book)
	{
		$this->authorize('makeAccepted', $book);

		$book->load([
			'genres',
			'writers',
			'translators',
			'sequences',
			'book_keywords'
		]);

		$validator = Validator::make($book->toArray(), (new StoreBook)->rules());

		if ($validator->fails())
			return redirect()
				->route('books.edit', $book)
				->withErrors($validator)
				->withInput();

		// отмечаем что книга принята в общую библиотеку
		$book->statusAccepted();
		$book->save();

		// отмечаем любых непроверенных авторов, как проверенные
		foreach ($book->authors()->unaccepted()->get() as $author) {
			$author->statusAccepted();
			$author->save();
		}

		// отмечаем любых непроверенных авторов, как проверенные
		foreach ($book->sequences()->unaccepted()->get() as $sequence) {
			$sequence->statusAccepted();
			$sequence->save();
		}

		$book_keywords = $book->book_keywords()->unaccepted()->get();

		if ($book_keywords->count() > 0) {
			foreach ($book_keywords as $book_keyword) {
				$book_keyword->statusAccepted();
				$book_keyword->save();
			}

			BookKeyword::flushCachedOnModerationCount();
		}

		foreach ($book->files()->unaccepted()->get() as $file) {
			$file->statusAccepted();
			$file->save();
		}

		$book->genres->each(function ($genre) {
			UpdateGenreBooksCount::dispatch($genre);
		});

		$book->authors->each(function ($author) {
			UpdateAuthorBooksCount::dispatch($author);
			$author->flushUsersAddedToFavoritesNewBooksCount();
		});

		$book->sequences->each(function ($sequence) {
			UpdateSequenceBooksCount::dispatch($sequence);
		});

		UpdateBookFilesCount::dispatch($book);
		Book::flushCachedOnModerationCount();
		Book::cachedCountRefresh();
		BookFile::flushCachedOnModerationCount();

		activity()->performedOn($book)->log('make_accepted');

		return redirect()
			->route('books.show', $book)
			->with('success', __('book.published'));
	}

	/**
	 * Форма установки доступа к чтению и скачиванию
	 *
	 * @param Book $book
	 * @return View
	 * @throws
	 */

	public function accessEdit(Book $book)
	{
		$this->authorize('change_access', $book);

		return view('book.access', compact('book'));
	}

	/**
	 * Устанавливаем доступ
	 *
	 * @param Request $request , Book $book
	 * @return Response
	 * @throws
	 */
	public function accessSave(Request $request, Book $book)
	{
		if ($book->isForSale()) {
			if (empty($request->download_access) and empty($request->read_access)) {
				if ($book->bought_times_count > 0) {
					return redirect()
						->route('books.sales.edit', $book)
						->withErrors([__('book.please_remove_the_book_from_sale_to_completely_block_access_to_the_book')]);
				}
			}
		}

		$this->authorize('change_access', $book);

		$this->validate($request, [
			'read_access' => 'boolean',
			'download_access' => 'boolean',
			'secret_hide_reason' => 'string|nullable'
		], [], __('book'));

		if ($request->read_access == $book->read_access and $request->download_access == $book->download_access and $request->secret_hide_reason == $book->secret_hide_reason) {
			return redirect()
				->route('books.access.edit', $book);
		}

		if ($request->read_access) {
			if ($book->characters_count < 100)
				return redirect()
					->route('books.access.edit', $book)
					->withErrors([__('book.a_book_must_have_at_least100_characters_in_order_to_be_able_to_read')]);
		}

		if ($request->download_access) {
			if ($book->files_count < 1)
				return redirect()
					->route('books.access.edit', $book)
					->with('show_how_to_attach_a_file', true)
					->withErrors([__('book.to_access_the_download_at_least_one_file_must_be_attached_to_the_book')]);
		}

		$book->fill($request->all());

		if ($book->isForSale()) {
			if (empty($request->download_access) and empty($request->read_access)) {
				$book->changePrice(0);
				$book->price_updated_at = null;
			}
		}

		$book->save();

		activity()
			->performedOn($book)
			->withProperties([
				'read_access' => $book->read_access,
				'download_access' => $book->download_access,
				'secret_hide_reason' => $book->secret_hide_reason,
			])
			->log('change_access');

		return redirect()
			->route('books.access.edit', $book)
			->with('success', __('book.access_settings_have_been_successfully_changed'));
	}

	/**
	 * Закрываем доступ к книге
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function close_access(Book $book)
	{
		if ($book->isForSale()) {
			return redirect()
				->route('books.access.edit', $book);
			/*
			if ($book->bought_times_count > 0)
			{
				return redirect()
					->route('books.sales.edit', $book)
					->withErrors([__('book.please_remove_the_book_from_sale_to_completely_block_access_to_the_book')]);
			}
			*/
		}

		$this->authorize('change_access', $book);

		$book->readAccessDisable();
		$book->downloadAccessDisable();

		if ($book->isForSale()) {
			if ($book->bought_times_count < 1) {
				$book->changePrice(0);
				$book->price_updated_at = null;
			}
		}

		$book->save();

		activity()
			->performedOn($book)
			->withProperties([
				'read_access' => false,
				'download_access' => false,
				'secret_hide_reason' => '',
			])
			->log('change_access');

		return back()
			->with(['success' => __('book.access_closed')]);
	}

	/**
	 * Возвращаем книгу в очередь на обработку
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */

	public function retryFailedParse(Book $book)
	{
		$this->authorize('retry_failed_parse', $book);

		$book->parse->associateAuthUser();
		$book->parse->wait();
		$book->push();

		return back();
	}

	/**
	 * Отменить парсинг файла книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */

	public function cancelParse(Book $book)
	{
		$this->authorize('cancel_parse', $book);

		$book->parse->success();
		$book->push();

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.parse_canceled')]);
	}

	/*
		public function moveForm()
		{
			$books = Book::find(explode(',', request()->books));

			return view('book.selected', ['books' => $books]);
		}

		public function move(Request $request)
		{
			//dd($request->all());

			$this->validate($request, [
				'books' => 'required|array|exists:books,id',
				'author' => 'required|number|exists:authors,id'
			]);
		}
		*/
	/*
		public function publishers()
		{
			$array = Book::selectRaw('"pi_pub" as value, count("pi_pub") as count')
				->where("pi_pub", '!=', '')
				->whereNotNull("pi_pub")
				->groupBy("pi_pub")
				->orderBy('count', 'desc')
				->limit(300)
				->get();

			return $array;
		}

		public function publish_city()
		{
			$array = Book::selectRaw('"pi_city" as value, count("pi_city") as count')
				->where("pi_city", '!=', '')
				->whereNotNull("pi_city")
				->groupBy("pi_city")
				->orderBy('count', 'desc')
				->limit(300)
				->get();

			return $array;
		}
	*/

	public function votes(Book $book)
	{
		$votes = $book->votes()
			->orderBy('user_updated_at', 'desc')
			->simplePaginate();

		return view('book.votes', compact('book', 'votes'));
	}

	public function activity_logs(Book $book)
	{
		$activityLogs = $book->activities()
			->latest()
			->simplePaginate();

		$activityLogs->load(['causer', 'subject' => function ($query) {
			$query->any();
		}]);

		return view('activity_log.index', compact('activityLogs'));
	}

	public function makePublicRules(Book $book)
	{
		return view('book.make_accepted_rules', compact('book'));
	}

	public function addForReviewRules(Book $book)
	{
		return view('book.add_for_review_rules', compact('book'));
	}

	/**
	 * Обновление счетчиков книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function refreshCounters(Book $book)
	{
		Artisan::call('refresh:book_counters', ['id' => $book->id]);

		return redirect()
			->route('books.show', $book);
	}

	public function openComments(Book $book)
	{
		$this->authorize('open_comments', $book);

		$book->comments_closed = false;
		$book->save();

		activity()
			->performedOn($book)
			->log('comments_open');

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.comments_opened')]);
	}

	public function closeComments(Book $book)
	{
		$this->authorize('close_comments', $book);

		$book->comments_closed = true;
		$book->save();

		activity()
			->performedOn($book)
			->log('comments_close');

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.comments_closed')]);
	}

	public function stopReading(Book $book)
	{
		$remembered_page = $book->remembered_pages()
			->where('user_id', auth()->id());

		$remembered_page->delete();

		return $remembered_page->first();
	}

	public function setAsNewReadOnlineFormat(Book $book)
	{
		$this->authorize('set_as_new_read_online_format', $book);

		$book->online_read_new_format = true;
		$book->save();

		return back();
	}

	public function salesEdit(Book $book)
	{
		$this->authorize('author', $book);

		$manager = $book->getManagerAssociatedWithUser(auth()->user());

		$chapterWithAMaximumOfSizeCharacters = $book->sections()
			->chapter()
			->orderBy('character_count', 'desc')
			->first();

		if (!empty($chapterWithAMaximumOfSizeCharacters)) {
			if ($chapterWithAMaximumOfSizeCharacters->character_count > config('litlife.max_section_characters_count')) {
				$isChapterWithExceedingTheNumberOfCharactersExists = true;
			}
		}

		$seller = $book->seller();
		$seller_manager = $book->seller_manager();

		if (is_object($seller_manager))
			$author = $seller_manager->manageable;
		else
			$author = false;

		return view('book.sales.edit', [
			'book' => $book,
			'manager' => $manager,
			'seller' => $seller,
			'seller_manager' => $seller_manager,
			'author' => $author,
			'freeFragmentCharactersPercentage' => $book->getFreeFragmentCharactersPercentage(),
			'isChapterWithExceedingTheNumberOfCharactersExists' => $isChapterWithExceedingTheNumberOfCharactersExists ?? false
		]);
	}

	public function salesSave(Request $request, Book $book)
	{
		$this->authorize('change_sell_settings', $book);

		if ($book->is_lp)
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors([__('book.amateur_translations_cannot_be_sold')]);

		if ($book->writers()->count() > 1)
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors([__('book.we_dont_have_the_opportunity_to_sell_books_with_more_than_one_writer')]);

		if ($book->ready_status == 'complete_but_publish_only_part' or $book->ready_status == 'not_complete_and_not_will_be')
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors([__('book.only_finished_or_in_the_process_of_writing_books_are_allowed_to_be_sold')]);

		$chapterWithAMaximumOfSizeCharacters = $book->sections()
			->chapter()
			->orderBy('character_count', 'desc')
			->first();

		if (!empty($chapterWithAMaximumOfSizeCharacters)) {
			if ($chapterWithAMaximumOfSizeCharacters->character_count > config('litlife.max_section_characters_count')) {
				return redirect()
					->route('books.sales.edit', $book)
					->withErrors([__('book.book_should_be_divided_into_chapters_and_parts', ['max_symbols_count' => config('litlife.max_section_characters_count')])]);
			}
		}

		if (empty($book->cover))
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors([__('book.book_must_have_a_cover_for_sale')]);

		if (empty($book->annotation) or $book->annotation->character_count < config('litlife.min_annotation_characters_count_for_sale'))
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors([__('book.annotation_must_contain_at_least_characters_for_sale', ['characters_count' => config('litlife.min_annotation_characters_count_for_sale')])]);

		if (empty($request->price))
			$request->request->add(['price' => null]);

		if ($book->seller_manager()->manageable->books()->whereReadyStatus('complete')->count() < 1) {
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors(['price' => __('book.for_the_sale_of_an_unfinished_book_you_must_have_at_least_one_completed_book_added')]);
		}

		if (!$book->isUserCreator(auth()->user())) {
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors(['price' => __('book.book_added_by_another_user')]);
		}

		if (!$book->is_si) {
			if ($book->isPrivate()) {
				return redirect()
					->route('books.sales.edit', $book)
					->withErrors([__('book.please_go_to_the_book_description_editing_page_and_set_the_status_samizdat')]);
			} else {
				return redirect()
					->route('books.sales.edit', $book)
					->withErrors([__('book.please_write_to_the_topic_ask_a_moderator_if_you_need_to_set_the_status_of_si')]);
			}
		}

		$this->validate($request, [
			'price' => 'nullable|numeric|min:' . config('litlife.min_book_price') . '|max:' . config('litlife.max_book_price') . '',
			'free_sections_count' => 'nullable|integer'
		], [], __('book'));

		if ($request->free_sections_count > 0 and $request->price < 1)
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors(['price' => __('book.number_of_free_chapters_must_be_zero_if_the_book_has_no_price')]);

		if ($request->free_sections_count >= $book->sections_count)
			return redirect()
				->route('books.sales.edit', $book)
				->withErrors(['free_sections_count' => __('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count])]);

		if (!empty($request->price)) {
			if ($book->characters_count < config('litlife.minimum_characters_count_before_book_can_be_sold')) {
				return redirect()
					->route('books.sales.edit', ['book' => $book])
					->withErrors(['error' => __('book.minimum_characters_count_before_book_can_be_sold', ['characters_count' => config('litlife.minimum_characters_count_before_book_can_be_sold')])]);
			}
		}

		DB::beginTransaction();

		if ($book->price != $request->price) {
			$days = $book->getDiffBeetweenLastPriceChangeInDays();

			if ($days > 0)
				return redirect()
					->route('books.sales.edit', ['book' => $book])
					->withErrors(['price' => trans_choice('book.book_price_cant_changed_within_period_days', $days, ['days_count' => $days])]);

			$book->changePrice($request->price);
		}

		$book->free_sections_count = $request->free_sections_count;
		$book->save();

		DB::commit();

		return redirect()
			->route('books.sales.edit', ['book' => $book])
			->with('success', __('common.data_saved'));
	}

	public function purchase(Request $request, Book $book)
	{
		$this->authorize('buy', $book);

		return view('book.purchase', compact('book'));
	}

	public function buy(Request $request, Book $book)
	{
		$this->authorize('buy', $book);

		$buyer = auth()->user();

		if ($buyer->balance < $book->price)
			return redirect()
				->route('books.show', ['book' => $book])
				->withErrors(['errors' => __('user_purchases.you_dont_have_enough_money')], 'buy');

		BookPurchaseJob::dispatch($book, $buyer, $book->seller());

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('user_purchases.you_successful_purchase_a_book')]);
	}

	public function buyDeposit(Request $request, Book $book)
	{
		$this->authorize('buy', $book);

		$this->validate($request, [
			'payment_type' => 'required|in:' . implode(',', config('unitpay.allowed_payment_types')) . ''
		], [], __('user_incoming_payment'));

		$user = auth()->user();

		$sum = $book->price;

		DB::beginTransaction();

		$payment = new UserIncomingPayment;
		$payment->payment_type = $request->payment_type;
		$payment->user_id = $user->id;
		$payment->ip = request()->ip();
		$payment->currency = 'RUB';
		$payment->payment_aggregator = 'unitpay';
		$payment->params = [];
		$payment->save();

		$transaction = new UserPaymentTransaction;
		$transaction->user_id = $user->id;
		$transaction->sum = $sum;
		$transaction->statusWait();
		$transaction->typeDeposit();
		$transaction->params = ['buy_book' => $book->id];

		$payment->transaction()->save($transaction);

		DB::commit();

		$params['sum'] = $sum;
		$params['account'] = $transaction->id;
		$params['desc'] = __('user_incoming_payment.desc_buy_book', ['title' => $book->getSellTitle(), 'sum' => $params['sum']]);
		$params['currency'] = 'RUB';
		$params['backUrl'] = route('books.show', ['book' => $book]);
		/*
		$params['hideMenu'] = true;
		$params['hideOtherPSMethods'] = true;
	*/
		$url = UnitPay::getFormUrl($request->payment_type, $params);

		return redirect()->away($url);
	}

	/**
	 * Снять с продажи книгу
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function removeFromSale(Book $book)
	{
		$this->authorize('remove_from_sale', $book);

		$book->statusReject();
		$book->changePrice(0);
		$book->save();

		foreach ($book->boughtUsers as $user) {
			$user->notify(new BookRemovedFromSaleNotification($book));
		}

		return redirect()
			->route('books.sales.edit', ['book' => $book])
			->with(['success' => __('book.removed_from_sale')]);
	}

	public function readRedirect(Book $book)
	{
		if (auth()->check()) {
			$rememberedPage = $book->remembered_pages
				->where('user_id', auth()->id())
				->first();
		}

		if ($book->isPagesNewFormat()) {
			if (empty($rememberedPage) or empty($rememberedPage->inner_section_id)) {
				$firstSection = $book->sections()
					->accepted()
					->chapter()
					->defaultOrder()
					->first();

				if (empty($firstSection))
					return redirect()->route('books.sections.index', ['book' => $book]);
				else
					return redirect()->route('books.sections.show', [
						'book' => $book,
						'section' => $firstSection->inner_id
					]);
			} else {
				return redirect()->route('books.sections.show', [
					'book' => $book,
					'section' => $rememberedPage->inner_section_id,
					'page' => $rememberedPage->page
				]);
			}
		} else {
			if (empty($rememberedPage)) {
				return redirect()->route('books.old.page', $book);
			} else {
				return redirect()->route('books.old.page', [
					'book' => $book,
					'page' => $rememberedPage->page
				]);
			}
		}
	}

	public function search(Request $request)
	{
		$str = trim($request->input('q'));

		if (is_numeric($str)) {
			$books = Book::acceptedOrBelongsToAuthUser()
				->where('id', $str)
				->simplePaginate();
		} else {

			$books = Book::titleAuthorsFulltextSearch($str)
				->acceptedOrBelongsToAuthUser()
				//->orderByRatingDesc()
				->simplePaginate();
			/*
						$books = Book::search($str)
							->rule(function ($builder) {
								return [
									'must' => [
										'multi_match' => [
											'query' => $builder->query,
											'fields' => [
												"title^2",
												"authors",
												"pi_isbn"
											],
											"operator" => "and",
											'type' => 'cross_fields'
										],
									],
									'filter' => [
										'term' => [
											'status' => StatusEnum::Accepted
										]
									]
								];
							})
							->paginate(10);
						*/
		}

		$books->load(['authors', 'sequences']);

		foreach ($books as $book) {
			$book->setRelation('writers', $book->getAuthorsWithType(AuthorEnum::Writer));
			$book->setRelation('editors', $book->getAuthorsWithType(AuthorEnum::Editor));
			$book->setRelation('illustrators', $book->getAuthorsWithType(AuthorEnum::Illustrator));
			$book->setRelation('translators', $book->getAuthorsWithType(AuthorEnum::Translator));
			$book->setRelation('compilers', $book->getAuthorsWithType(AuthorEnum::Compiler));
		}

		return new BookCollection($books);
	}

	public function enterBlockingList()
	{
		$this->authorize('blockAccessByList', Book::class);

		if (session('book_ids')) {
			$books = Book::whereIn('id', session('book_ids'))
				->with('writers')
				->get();
		}

		return view('book.block_access_by_list.form', ['books' => $books ?? null]);
	}

	public function disableAccessByList(Request $request)
	{
		$this->authorize('blockAccessByList', Book::class);

		$this->validate($request, [
			'text' => 'required|string',
			'reason_for_changing_access' => 'required|string'
		], [], [
			'text' => __('common.text'),
			'reason_for_changing_access' => __('book.reason_for_changing_access')
		]);

		$text = $request->text;

		$hosts = config('litlife.site_hosts');

		$hosts = array_map(function ($host) {
			return preg_quote($host);
		}, $hosts);

		preg_match_all('/(' . implode('|', $hosts) . ')\/books\/([0-9]+)/iu', $text, $matches);

		foreach ($matches[2] as $match) {
			$id = intval($match);

			if (!empty($id))
				$ids[] = $id;
		}

		$blocked_book_ids = [];

		if (empty($ids) or count($ids) < 1)
			return back()
				->withInput($request->all())
				->withErrors([__('book.no_links_to_the_book_were_found_in_the_text')]);

		Book::whereIn('id', $ids)
			->readOrDownloadAccess()
			->chunkById(100, function ($books) use (&$blocked_book_ids, $request) {
				foreach ($books as $book) {
					if (auth()->user()->can('change_access', $book)) {
						if (!$book->isForSale()) {
							$book->readAccessDisable();
							$book->downloadAccessDisable();
							$book->secret_hide_reason = $request->reason_for_changing_access;
							$book->save();

							activity()
								->performedOn($book)
								->withProperties([
									'read_access' => false,
									'download_access' => false,
									'secret_hide_reason' => $request->reason_for_changing_access,
								])
								->log('change_access');

							$blocked_book_ids[] = $book->id;
						}
					}
				}
			});

		return back()
			->with([
				'success' => __('book.access_to_the_specified_books_is_closed'),
				'book_ids' => $blocked_book_ids
			]);
	}

	public function enableForbidChangesInBook(Book $book)
	{
		$this->authorize('enableForbidChangesInBook', $book);

		$book->forbid_to_change = true;
		$book->save();

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.forbid_changes_enabled')]);
	}

	public function disableForbidChangesInBook(Book $book)
	{
		$this->authorize('disableForbidChangesInBook', $book);

		$book->forbid_to_change = false;
		$book->save();

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.forbid_changes_disabled')]);
	}

	/**
	 * Отображает форму для ввода ID книги, которую нужно заменить
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function listOfBooksAddedByOtherUsersForm(Request $request, Book $book)
	{
		$this->authorize('replaceWithThis', $book);

		return view('book.replace_created_by_other_user.form', ['book' => $book]);
	}

	/**
	 * Заменяет книгу добавленную другим пользователем. Текущую делает главным изданием, а у другой закрывает доступ
	 * к чтению и скачиванию
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function replaceBookCreatedByAnotherUser(Request $request, Book $book)
	{
		$this->authorize('replaceWithThis', $book);

		$this->validate($request, ['book_id' => 'required|integer'], [], __('book'));

		$replacedBook = Book::find($request->book_id);

		if (empty($replacedBook))
			return redirect()->route('books.replace_book_created_by_another_user.form', $book)
				->withErrors(['book_id' => __('book.the_book_with_this_id_was_not_found')]);

		if ($replacedBook->id == $book->id)
			return redirect()->route('books.replace_book_created_by_another_user.form', $book)
				->withErrors(['book_id' => __('book.id_of_the_book_being_replaced_must_not_match_the_id_of_the_book_being_replaced')]);

		if ($replacedBook->isUserCreator(auth()->user()))
			return redirect()->route('books.replace_book_created_by_another_user.form', $book)
				->withErrors(['book_id' => __('book.enter_the_id_of_the_book_that_another_user_added')]);

		if (optional($replacedBook->getManagerAssociatedWithUser(auth()->user()))->character != 'author')
			return redirect()->route('books.replace_book_created_by_another_user.form', $book)
				->withErrors(['book_id' => __('book.enter_the_id_of_the_book_that_belongs_to_your_author_page')]);

		$this->authorize('replaceThis', $replacedBook);

		DB::transaction(function () use ($request, $book, $replacedBook) {

			$replacedBook->readAccessDisable();
			$replacedBook->downloadAccessDisable();
			$replacedBook->save();

			BookGroupJob::dispatch($book, $replacedBook, true, true, false);

			$book->main_in_group = true;
			$book->save();
		});

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.you_have_successfully_replaced_your_book_with_a_book_added_by_another_user')]);
	}

	/**
	 * Список книг, которые добавили другие пользователи
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function listOfBooksAddedByOtherUsers(Book $book)
	{
		$this->authorize('replaceWithThis', $book);

		$author = $book->authors()->firstOrFail();

		$books = $author->books()
			->where('create_user_id', '!=', auth()->id())
			->simplePaginate();

		return view('book.replace_created_by_other_user.book_list', ['books' => $books]);
	}

	/**
	 * Удаление всех глав, изображений (кроме обложки) и файлов книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function deletingOnlineReadAndFiles(Book $book)
	{
		$book->deletingOnlineReadAndFiles();

		activity()->performedOn($book)
			->log('deleting_online_read_and_files');

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book.removed_all_files_chapters_footnotes_and_images_of_the_book')]);
	}

	/**
	 * Форма для изменения даты рейтинга
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function editDateOfRating(Book $book)
	{
		$user = auth()->user();

		if (!empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		$user_rating = $mainBook->votes()
			->where('create_user_id', $user->id)
			->firstOrFail();

		if ($user_rating->user_updated_at == null)
			$user_rating->user_updated_at = Carbon::parse('2012-10-02 16:48:05');

		return view('book.edit_date_of_rating',
			[
				'book' => $book,
				'user_rating' => $user_rating,
				'user_updated_at' => $user_rating->user_updated_at->timezone(session()->get('geoip')->timezone)
			]);
	}

	/**
	 * Изменение даты рейтинга
	 *
	 * @param StoreDate $request
	 * @param Book $book
	 * @return array
	 * @throws
	 */
	public function updateDateOfRating(StoreDate $request, Book $book)
	{
		$user = auth()->user();

		if (!empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		$user_rating = $mainBook->votes()
			->where('create_user_id', $user->id)
			->firstOrFail();

		$date = Carbon::createSafe($request->year, $request->month, $request->day,
			$request->hour, $request->minute, $request->second, session()->get('geoip')->timezone);

		$user_rating->user_updated_at = $date->setTimezone('UTC');
		$user_rating->save();

		return view('book.date_of_rating', [
			'book' => $book,
			'user_rating' => $user_rating
		]);
	}

	/**
	 * Форма для изменения даты статуса прочитанности
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function editDateOfReadStatus(Book $book)
	{
		$user = auth()->user();

		if (!empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		$user_read_status = $mainBook->users_read_statuses()
			->where('user_id', $user->id)
			->firstOrFail();

		if ($user_read_status->user_updated_at == null)
			$user_read_status->user_updated_at = Carbon::parse('2012-10-02 16:48:05');

		return view('book.read_status.date.edit',
			[
				'book' => $book,
				'user_read_status' => $user_read_status,
				'user_updated_at' => $user_read_status->user_updated_at->timezone(session()->get('geoip')->timezone)
			]);
	}

	/**
	 * Изменение даты статуса прочитанности
	 *
	 * @param StoreDate $request
	 * @param Book $book
	 * @return array
	 * @throws
	 */
	public function updateDateOfReadStatus(StoreDate $request, Book $book)
	{
		$user = auth()->user();

		if (!empty($book->mainBook))
			$mainBook = $book->mainBook;
		else
			$mainBook = $book;

		$user_read_status = $mainBook->users_read_statuses()
			->where('user_id', $user->id)
			->firstOrFail();

		$date = Carbon::createSafe($request->year, $request->month, $request->day,
			$request->hour, $request->minute, $request->second, session()->get('geoip')->timezone);

		$user_read_status->user_updated_at = $date->setTimezone('UTC');
		$user_read_status->save();

		return view('book.read_status.date.show', [
			'book' => $book,
			'user_read_status' => $user_read_status
		]);
	}

	/**
	 * Обложка книги в полном размере
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function cover(Request $request, Book $book)
	{
		if (empty($book->cover))
			abort(404);

		if ($request->ajax())
			return view('book.cover.show', ['book' => $book])
				->renderSections()['cover'];
		else
			return view('book.cover.show', ['book' => $book]);
	}

	/**
	 * Список подборок к которым принадлежит книга
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function collections(Request $request, Book $book)
	{
		$builder = $book->collections()
			->acceptedOrBelongsToAuthUser();

		$resource = (new CollectionSearchResource(request(), $builder));
		$vars = $resource->getVars();

		$vars['collections'] = $resource->getQuery()
			->with('latest_books.writers')
			->simplePaginate();

		if (auth()->check()) {
			$vars['collections']->load(['collectionUser' => function ($query) {
				$query->where('user_id', auth()->id());
			}]);
		}

		$vars['collections']->load('authUserLike');

		if (request()->ajax())
			return view('collection.list', $vars);

		return view('book.collections', $vars);
	}

	/**
	 * Страница для выбора подборки в которую нужно добавить книгу
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function collectionCreate(Request $request, Book $book)
	{
		$this->authorize('addToCollection', $book);

		if (old('collection_id'))
			$collection = Collection::findOrFail(old('collection_id'));

		return view('book.collection.create', [
			'book' => $book,
			'collection' => $collection ?? null
		]);
	}

	/**
	 * Список подборок к которым принадлежит книга
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function collectionSearch(Request $request, Book $book)
	{
		$query = Collection::query()
			->whereUserCanAddBooks(auth()->user())
			->with('create_user');

		if (!empty($request->search)) {
			$query->fulltextSearch($request->search);
		} else {
			$latestCollectionsIds = CollectedBook::selectRaw('collection_id, MAX("created_at") AS latest_created_at')
				->whereCreator(auth()->user())
				->groupBy('collection_id')
				->orderBy('latest_created_at', 'desc')
				->limit(10)
				->get()
				->pluck('collection_id')
				->toArray();

			if (count($latestCollectionsIds) > 0) {
				$query->whereIn('id', $latestCollectionsIds)
					->orderByField('id', $latestCollectionsIds);
			}
		}

		$query->with(['books' => function ($query) use ($book) {
			$query->where('books.id', $book->id);
		}]);

		$collections = $query->simplePaginate();

		return view('book.collection.list', [
			'book' => $book,
			'collections' => $collections
		]);
	}

	/**
	 * Отображение выбранной подборки
	 *
	 * @param Book $book
	 * @param Collection $collection
	 * @return Response
	 * @throws
	 */
	public function collectionSelected(Book $book, Collection $collection)
	{
		$this->authorize('addToCollection', $book);
		$this->authorize('addBook', $collection);

		return view('book.collection.selected', ['collection' => $collection]);
	}

	/**
	 * Список подборок к которым принадлежит книга
	 *
	 * @param StoreBookCollected $request
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function collectionStore(StoreBookCollected $request, Book $book)
	{
		$this->authorize('addToCollection', $book);

		$collection = Collection::findOrFail($request->collection_id);

		$this->authorize('addBook', $collection);

		CollectedBook::updateOrCreate(
			[
				'collection_id' => $collection->id,
				'book_id' => $book->id
			],
			[
				'number' => $request->number,
				'comment' => $request->comment
			]
		);

		$collection->latest_updates_at = now();
		$collection->save();

		return redirect()
			->route('books.show', $book)
			->with('success', __('The book was successfully added to the collection'));
	}
}
