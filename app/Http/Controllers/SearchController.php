<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\Collection;
use App\SearchQueriesLog;
use App\Sequence;
use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
	/**
	 * Отображение результатов
	 *
	 * @param string $query
	 * @param Request $request
	 * @return array
	 * @throws
	 */
	public function results(Request $request)
	{
		$query = trim($request->input('query'));

		$query = mb_substr($query, 0, 255);

		if (mb_strlen(preg_replace('/[^[:alnum:]]/iu', '', $query)) < config('litlife.minimum_number_of_letters_and_numbers')) {

			$view = view('search.min_length');
			if ($request->ajax()) {
				return $view->renderSections()['content'];
			} else {
				return $view;
			}
		}

		$booksQuery = Book::titleFulltextSearch($query)
			->acceptedOrBelongsToAuthUser()
			->orderByRatingDesc()
			->with(["authors", "genres", "sequences", "language",
				"originalLang", "short_annotation", 'group', 'cover',
				'authors.managers', 'parse',
				'remembered_pages' => function ($query) {
					$query->where('user_id', auth()->id());
				}])
			->with(['remembered_pages' => function ($query) {
				$query->where('user_id', auth()->id());
			}])
			->with(['statuses' => function ($query) {
				$query->where('user_id', auth()->id());
			}])
			->with(['votes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			}]);

		$authorsQuery = Author::fulltextSearch($query)
			->notMerged()
			->orderByRating()
			->acceptedOrBelongsToAuthUser()
			->with(['managers.user', 'photo']);

		$sequencesQuery = Sequence::fulltextSearch($query)
			->notMerged()
			->orderByBooksCountAsc()
			->acceptedOrBelongsToAuthUser();

		$usersQuery = User::fulltextSearch($query)
			->active()
			->orderByPostsCountDesc()
			->with(['groups', 'latest_user_achievements', 'avatar']);

		$collectionsQuery = Collection::fulltextSearch($query)
			->acceptedOrBelongsToAuthUser()
			->orderByLikesCount()
			->with([
				'create_user.avatar',
				'authUserLike',
				'usersAddedToFavorites' => function ($query) {
					$query->where('user_id', auth()->id());
				},
				'likes' => function ($query) {
					$query->where('create_user_id', auth()->id());
				},
			]);

		$collections = $collectionsQuery->limit(3)->get();

		if (auth()->check()) {
			$collections->load(['collectionUser' => function ($query) {
				$query->where('user_id', auth()->id());
			}]);
		}
		/*
				$topicsQuery = Topic::fulltextSearch($query);
		*/

		SearchQueriesLog::create([
			'query_text' => $query,
			'user_id' => auth()->id()
		]);

		$array = [
			'query' => $query,
			'books_count' => $booksQuery->count(),
			'books' => $booksQuery->limit(3)->get(),
			'books_url' => route('books', ['search' => $query, 'order' => 'rating_avg_down',
				'paid_access' => 'any', 'read_access' => 'any', 'download_access' => 'any']),
			'authors_count' => $authorsQuery->count(),
			'authors' => $authorsQuery->limit(3)->get(),
			'authors_url' => route('authors', ['search' => $query, 'order' => 'rating']),
			'sequences_count' => $sequencesQuery->count(),
			'sequences' => $sequencesQuery->limit(3)->get(),
			'sequences_url' => route('sequences', ['search' => $query, 'order' => 'book_count_desc']),
			'users_count' => $usersQuery->count(),
			'users' => $usersQuery->limit(3)->get(),
			'users_url' => route('users', ['search' => $query]),
			'collections_count' => $collectionsQuery->count(),
			'collections' => $collections,
			'collections_url' => route('collections.index', ['search' => $query, 'order' => 'likes_count_desc']),
			/*
			'topics_count' => $topicsQuery->count(),
			'topics' => $topicsQuery->limit(3)->get(),
			'topics_url' => route('topics.search', ['search' => $query]),
			*/
		];

		if ($request->ajax()) {
			return view('search.results_ajax', $array);
		} else {
			return view('search.results', $array);
		}
	}

	public function input()
	{
		return view('search.input');
	}

	public function google()
	{
		return view('search.google');
	}
}
