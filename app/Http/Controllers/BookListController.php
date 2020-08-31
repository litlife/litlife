<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\BookGroup;
use App\Enums\ReadStatus;
use App\Library\BookSearchResource;
use App\Sequence;
use App\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookListController extends Controller
{
	/**
	 * Удаленные книги
	 *
	 * @param Request $request
	 * @return View
	 */
	public function trashed(Request $request)
	{
		$builder = Book::acceptedAndSentForReview()->onlyTrashed();

		$resource = (new BookSearchResource($request, $builder))
			->setViewType('book.list.trashed')
			->defaultSorting('deleted_at_desc')
			->addOrder('deleted_at_asc', function ($query) {
				$query->orderBy('books.deleted_at', 'asc');
			})
			->addOrder('deleted_at_desc', function ($query) {
				$query->orderBy('books.deleted_at', 'desc');
			});

		$vars = $resource->getVars();

		$vars['books'] = $resource->getQuery()
			->with('latestActivitiesItemDeleted.causer')
			->simplePaginate();

		if ($request->ajax())
			return view($resource->view_name, $vars);

		return view('book.search', $vars);
	}

	/**
	 * Список книг
	 *
	 * @param Request $request
	 * @return View
	 */
	public function index(Request $request)
	{
		$builder = Book::acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource($request, $builder))
			->defaultSorting('rating_week_desc')
			->setDefaultInputValue('hide_grouped', '1')
			->setDefaultInputValue('read_access', 'open');

		if (auth()->check()) {
			$resource->saveSettings();
		}

		return $resource->view();
	}

	/**
	 * Похожие книги
	 *
	 * @param Book $book
	 * @return View
	 */
	public function similar(Book $book)
	{
		$builder = $book->similars()
			->acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Книги с обновлениями текста
	 *
	 * @param User $user
	 * @return View
	 */
	public function updates(User $user)
	{
		$builder = $user->favoriteBooksWithUpdates();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}


	/**
	 * Добавленные книги пользователем
	 *
	 * @param User $user
	 * @return View
	 */
	public function userCreated(User $user)
	{
		$builder = $user->created_books()->withoutCheckedScope();

		$resource = (new BookSearchResource(request(), $builder))
			->defaultSorting('date_down')
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Книги автора
	 *
	 * @param Author $author
	 * @return View
	 */
	public function author(Author $author)
	{
		$builder = $author->books()
			->acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Книги из группы
	 *
	 * @param int $group
	 * @return View
	 */
	public function group($group)
	{
		$group = BookGroup::findOrFail(intval($group));

		$builder = $group->books()
			->acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Издания книги
	 *
	 * @param Book $book
	 * @return View
	 */
	public function editions(Book $book)
	{
		if ($book->isInGroup() and $book->isNotMainInGroup() and !$book->is($book->mainBook))
			return redirect()->route('books.editions.index', ['book' => $book->mainBook]);

		$builder = $book->groupedBooks()
			->acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Переведенные книги автором
	 *
	 * @param Author $author
	 * @return View
	 */
	public function author_translated_books(Author $author)
	{
		$builder = $author->translated_books()
			->acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Книги у серии
	 *
	 * @param Sequence $sequence
	 * @return View
	 * @throws
	 */
	public function sequence(Sequence $sequence)
	{
		$builder = $sequence->books()
			->acceptedOrBelongsToAuthUser()
			->withPivot('number');

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false)
			->defaultSorting('SequenceNumberAsc')
			->addOrder('SequenceNumberAsc', function ($query) {
				$query->orderBy('book_sequences.number', 'asc');
			})
			->addOrder('SequenceNumberDesc', function ($query) {
				$query->orderBy('book_sequences.number', 'desc');
			});

		return $resource->view();
	}

	/**
	 * Купленные книги
	 *
	 * @param User $user
	 * @return View
	 */
	public function purchased(User $user)
	{
		$builder = $user->purchased_books()
			->wherePivot('canceled_at', null);

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Книги на проверке
	 *
	 * @return View
	 */
	public function books_on_moderation()
	{
		$builder = Book::sentOnReview();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false)
			->addOrder('oldest_sent_for_review', function ($query) {
				$query->orderStatusChangedAsc();
			})
			->addOrder('latest_sent_for_review', function ($query) {
				$query->orderStatusChangedDesc();
			})
			->defaultSorting('oldest_sent_for_review');

		return $resource->view();
		/*
				$this->before();

				$this->query = Book::sentOnReview();

				$this->order_array['oldest_sent_for_review'] = function () {
					$this->query->orderStatusChangedAsc();
				};

				$this->order_array['latest_sent_for_review'] = function () {
					$this->query->orderStatusChangedDesc();
				};

				$this->simple_paginate = false;

				$this->defaultSorting = 'oldest_sent_for_review';

				$this->search_parameters();
				return $this->view();
				*/
	}

	/**
	 * Прочитанные книги
	 *
	 * @param User $user
	 * @return View
	 */
	public function user_readed(User $user)
	{
		return $this->with_user_status($user, ReadStatus::Readed);
	}

	/**
	 * Со статустом пользователя
	 *
	 * @param User $user
	 * @param string $status
	 * @return View
	 */
	public function with_user_status(User $user, $status)
	{
		$builder = $user->books_read_statuses()
			->withPivot('user_updated_at')
			->where('book_statuses.status', $status)
			->any();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false)
			->defaultSorting('last_status_change')
			->disableFilter('read_status')
			->addOrder('last_status_change', function ($query) {
				$query->orderByRaw('book_statuses.user_updated_at desc NULLS last')
					->orderBy('book_statuses.book_id', 'desc');
			})
			->addOrder('first_status_change', function ($query) {
				$query->orderByRaw('book_statuses.user_updated_at asc NULLS first')
					->orderBy('book_statuses.book_id', 'asc');
			});

		return $resource->view();
	}

	/**
	 * Прочитать позже
	 *
	 * @param User $user
	 * @return View
	 */
	public function user_read_later(User $user)
	{

		return $this->with_user_status($user, ReadStatus::ReadLater);
	}

	/**
	 * Читает сейчас
	 *
	 * @param User $user
	 * @return View
	 */
	public function user_read_now(User $user)
	{

		return $this->with_user_status($user, ReadStatus::ReadNow);
	}

	/**
	 * Не дочитанные книги
	 *
	 * @param User $user
	 * @return View
	 */
	public function user_read_not_complete(User $user)
	{

		return $this->with_user_status($user, ReadStatus::ReadNotComplete);
	}

	/**
	 * Книги не читать
	 *
	 * @param User $user
	 * @return View
	 */
	public function user_not_read(User $user)
	{
		return $this->with_user_status($user, ReadStatus::NotRead);
	}

	/**
	 * Книги оцененные пользователем
	 *
	 * @param User $user
	 * @return View
	 */
	public function votes(User $user)
	{
		$builder = $user->bookThatRated()
			->any()
			->withPivot('vote', 'created_at', 'user_updated_at')
			->with(['users_read_statuses.user', 'users_read_statuses' => function ($query) use ($user) {
				$query->where('user_id', $user->id);
			}]);

		$resource = (new BookSearchResource(request(), $builder))
			->setViewType('book.list.votes')
			->setSimplePaginate(false)
			->addOrder('UserRateHigh', function ($query) {
				$query->orderBy('book_votes.vote', 'desc')
					->orderBy('books.id', 'desc');
			})
			->addOrder('UserBookLow', function ($query) {
				$query->orderBy('book_votes.vote', 'asc')
					->orderBy('books.id', 'desc');
			})
			->addOrder('UserBookRateFirst', function ($query) {
				$query->orderBy('book_votes.user_updated_at', 'asc');
			})
			->addOrder('UserBookRateLast', function ($query) {
				$query->orderBy('book_votes.user_updated_at', 'desc');
			})
			->disableFilter('read_status')
			->defaultSorting('UserBookRateLast');

		return $resource->view();
	}

	/**
	 * Книги в личной библиотеке
	 *
	 * @param User $user
	 * @return View
	 */
	public function userLibrary(User $user)
	{
		$builder = $user->books()->any();

		$resource = (new BookSearchResource(request(), $builder))
			->setSimplePaginate(false)
			->addOrder('MyLibraryAddTimeFirst', function ($query) {
				$query->orderBy('user_books.created_at', 'asc')
					->orderBy('id', 'desc');
			})
			->addOrder('MyLibraryAddTimeLast', function ($query) {
				$query->orderBy('user_books.created_at', 'desc')
					->orderBy('id', 'asc');
			})
			->defaultSorting('MyLibraryAddTimeLast');

		return $resource->view();
	}

	/**
	 * Книги избранных авторов
	 *
	 * @param User $user
	 * @return View
	 */
	public function favoriteAuthorsBooks(User $user)
	{
		if ($user->getNewFavoriteAuthorsBooksCount() > 0 and ($user->id == auth()->id())) {
			$user->data->favorite_authors_books_latest_viewed_at = now();
			$user->data->save();
			$user->flushCachedNewFavoriteAuthorsBooksCount();
		}
		/*
				$builder = $user->favorite_authors_books()
					->acceptedOrBelongsToAuthUser();
		*/
		$builder = $user->getFavoriteAuthorBooksBuilder();

		$resource = (new BookSearchResource(request(), $builder))
			->defaultSorting('OnShow_Down')
			->setSimplePaginate(false);

		return $resource->view();
	}
}
