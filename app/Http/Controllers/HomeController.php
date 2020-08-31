<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Book;
use App\Collection;
use App\Comment;
use App\Enums\AuthorEnum;
use App\Library\BookSearchResource;
use App\Post;
use App\Topic;
use App\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class HomeController extends Controller
{
	private $cookieMinutes = 2678400;

	/**
	 * Вывод главной страницы
	 *
	 * @param Request $request
	 * @return View
	 */
	public function index(Request $request)
	{
		$latestRoute = $request->cookie('latest_route');

		if ($latestRoute != 'home' and Route::has($latestRoute)) {
			return redirect()
				->route($latestRoute);
		} else {
			return $this->popular_books($request, 'week');
		}
	}

	/**
	 * Вывод последних книг
	 *
	 * @param Request $request
	 * @return View
	 */
	public function latest_books(Request $request)
	{
		$builder = Book::acceptedOrBelongsToAuthUser();

		$resource = (new BookSearchResource($request, $builder))
			->setDefaultInputValue('hide_grouped', '1')
			->setDefaultInputValue('read_access', 'open')
			->defaultSorting('OnShow_Down');

		if (auth()->check()) {
			$resource->saveSettings();
		}

		$vars = $resource->getVars();

		$vars['books'] = $resource->getQuery()->simplePaginate();

		if (request()->ajax())
			return $resource->renderAjax($vars);

		return response()
			->view('home.index', $vars)
			->withCookie(cookie('latest_route', Route::getCurrentRoute()->getName(), $this->cookieMinutes));
	}

	/**
	 * Вывод популярных книг
	 *
	 * @param Request $request
	 * @param $period
	 * @return View
	 */
	public function popular_books(Request $request, $period = 'week')
	{
		$query = Book::join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id')
			->orderBy($period . '_rating', 'desc')
			->where($period . '_rating', '>', 0)
			->notConnected()
			->with(['genres', 'sequences', 'short_annotation', 'cover', 'language', 'originalLang', 'group', 'files', 'authors.managers', 'parse'])
			->with(['remembered_pages' => function ($query) {
				$query->where('user_id', auth()->id());
			}])->with(['purchases' => function ($query) {
				$query->where('buyer_user_id', auth()->id());
			}]);

		if (auth()->check()) {
			$genres_blacklist = auth()->user()->genres_blacklist->pluck('genre_id')->toArray();

			if ($genres_blacklist) {
				$query->withoutGenre($genres_blacklist);
			}
		}

		$books = $query->simplePaginate();

		foreach ($books as $book) {
			$book->setRelation('writers', $book->getAuthorsWithType(AuthorEnum::Writer));
		}

		$response = response()
			->view('home.popular_books', compact('books', 'period'));

		if (Route::getCurrentRoute()->getName() == 'home')
			return $response;
		else
			return $response->withCookie(cookie('latest_route', Route::getCurrentRoute()->getName(), $this->cookieMinutes));
	}

	/**
	 * Вывод последних комментариев
	 *
	 * @param Request $request
	 * @return View
	 */
	public function latest_comments(Request $request)
	{
		$comments = Comment::latest()
			->acceptedOrBelongsToAuthUser()
			->showOnHomePage()
			->book()
			->whereHas('book', function ($query) {
				$query->acceptedAndSentForReview();
			})
			->simplePaginate();

		$comments->load('originCommentable')
			->loadMorph('originCommentable', [
				Book::class => [
					'authors.managers'
				],
				Collection::class => []
			])->loadMissing([
				"userBookVote",
				'create_user.avatar',
				'create_user.latest_user_achievements',
				"create_user.groups",
				"create_user.relationship",
				'votes' => function ($query) {
					$query->where("create_user_id", auth()->id());
				}
			]);

		return response()
			->view('home.latest_comments', compact('comments'))
			->withCookie(cookie('latest_route', Route::getCurrentRoute()->getName(), $this->cookieMinutes));
	}

	/**
	 * Вывод последних сообщений в блоге
	 *
	 * @param Request $request
	 * @return View
	 */
	public function latest_wall_posts(Request $request)
	{
		$blogs = Blog::latest()
			->roots()
			->owned()
			->where('display_on_home_page', true)
			->simplePaginate();

		$blogs->loadMissing(['owner.avatar', 'owner.account_permissions', 'owner.setting']);

		foreach ($blogs as $blog) {
			$blog->create_user = $blog->owner;
		}

		$blogs->loadMissing(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		return response()
			->view('home.latest_wall_posts', compact('blogs'))
			->withCookie(cookie('latest_route', Route::getCurrentRoute()->getName(), $this->cookieMinutes));
	}

	/**
	 * Вывод последних сообщений на форуме
	 *
	 * @param Request $request
	 * @return View
	 */
	public function latest_posts(Request $request)
	{
		$settings = Variable::where('name', 'settings')->first();

		$posts = Post::latest()
			->acceptedOrBelongsToAuthUser()
			->with(['create_user.latest_user_achievements.achievement.image', 'edit_user', 'topic', 'create_user.avatar', 'forum', 'likes', "create_user.groups"])
			->when(isset($settings->value['hide_from_main_page_forums']), function ($query) use ($settings) {
				return $query->whereNotIn('posts.forum_id', $settings->value['hide_from_main_page_forums']);
			})
			->select('posts.*')
			/*
			->whereHas('create_user', function ($query) {
			$query->where('forum_message_count', '>', 10);
		})
		*/
			->where('topics.hide_from_main_page', false)
			->withUserAccessToForums()
			->simplePaginate();


		$topics = Topic::with('last_post.create_user', 'forum')
			->when(isset($settings->value['hide_from_main_page_forums']), function ($query) use ($settings) {
				return $query->whereNotIn('topics.forum_id', $settings->value['hide_from_main_page_forums']);
			})
			->select('topics.*')
			->withUserAccessToForums()
			->where('hide_from_main_page', false)
			->orderBy('main_priority', 'desc')
			->orderByLastPostNullsLast()
			->limit(10)->get();

		$posts->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		return response()
			->view('home.latest_posts', compact('posts', 'topics'))
			->withCookie(cookie('latest_route', Route::getCurrentRoute()->getName(), $this->cookieMinutes));
	}
}
