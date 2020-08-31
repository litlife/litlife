<?php

namespace App\Http\Controllers;

use App\Author;
use App\Enums\ReadStatus;
use App\Http\SearchResource\AuthorSearchResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorListController extends Controller
{
	/**
	 * Список авторов
	 *
	 * @return View
	 */
	public function index(Request $request)
	{
		/*
		$this->before();

		$this->query = Author::notMerged()
			->acceptedOrBelongsToAuthUser();

		return $this->finaly();
*/

		$builder = Author::notMerged()
			->acceptedOrBelongsToAuthUser();

		$resource = (new AuthorSearchResource($request, $builder))
			->defaultSorting('rating_week_desc');

		return $resource->view();
	}

	/**
	 * Прочитанные авторы
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	public function user_readed(Request $request, User $user)
	{
		return $this->with_user_status($request, $user, ReadStatus::Readed);
	}

	/**
	 * Со статустом пользователя
	 *
	 * @param Request $request
	 * @param User $user
	 * @param string $status
	 * @return View
	 */
	public function with_user_status(Request $request, User $user, $status)
	{
		$builder = $user->authors_read_statuses()
			->where('author_statuses.status', $status)
			->any();

		$resource = (new AuthorSearchResource($request, $builder))
			->defaultSorting('rating')
			->setSimplePaginate(false)
			->addOrder('read_status_updated_at_desc', function ($query) {
				$query->orderByRaw('author_statuses.user_updated_at desc NULLS last')
					->orderBy('author_id', 'desc');
			})
			->addOrder('read_status_updated_at_asc', function ($query) {
				$query->orderByRaw('author_statuses.user_updated_at asc NULLS first')
					->orderBy('author_id', 'asc');
			});

		return $resource->view();
	}

	/**
	 * Прочитать атворов
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	public function user_read_later(Request $request, User $user)
	{
		return $this->with_user_status($request, $user, ReadStatus::ReadLater);
	}

	/**
	 * Читаемые авторы
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	public function user_read_now(Request $request, User $user)
	{
		return $this->with_user_status($request, $user, ReadStatus::ReadNow);
	}

	/**
	 * Не дочитанные авторы
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	public function user_read_not_complete(Request $request, User $user)
	{
		return $this->with_user_status($request, $user, ReadStatus::ReadNotComplete);
	}

	/**
	 * Авторы не читать
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	public function user_not_read(Request $request, User $user)
	{
		return $this->with_user_status($request, $user, ReadStatus::NotRead);
	}

	/**
	 * Авторы добавленные пользователем
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	public function userCreated(Request $request, User $user)
	{
		$builder = $user->created_authors()
			->withoutCheckedScope();

		$resource = (new AuthorSearchResource($request, $builder))
			->defaultSorting('created_at_desc')
			->setSimplePaginate(false);

		return $resource->view();
	}

	/**
	 * Избранные авторы
	 *
	 * @param Request $request
	 * @param User $user
	 * @return View
	 */
	function userLibrary(Request $request, User $user)
	{
		$builder = Author::any()->join('user_authors', function ($join) use ($user) {
			$join->on('authors.id', '=', 'user_authors.author_id')
				->where('user_authors.user_id', '=', $user->id);
		})->select('authors.*', 'user_authors.created_at');

		$resource = (new AuthorSearchResource($request, $builder))
			->defaultSorting('MyLibraryAddTimeFirst')
			->setSimplePaginate(false)
			->addOrder('MyLibraryAddTimeFirst', function ($query) {
				$query->orderBy('user_authors.created_at', 'asc')
					->orderBy('authors.id', 'desc');
			})
			->addOrder('MyLibraryAddTimeLast', function ($query) {
				$query->orderBy('user_authors.created_at', 'desc')
					->orderBy('authors.id', 'asc');
			});

		return $resource->view();
	}
}
