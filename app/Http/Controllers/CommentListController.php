<?php

namespace App\Http\Controllers;

use App\Author;
use App\Comment;
use App\Enums\UserRelationType;
use App\Library\CommentSearchResource;
use App\Sequence;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommentListController extends Controller
{
	public $input;
	public $query;
	public $item_render = 'default';
	public $defaultSorting = 'DateDesc';
	public $request;
	public $order_array;
	public $limit = 20;

	/**
	 * Список всех комментариев
	 *
	 * @return View
	 */
	function index()
	{
		$this->before();

		$builder = Comment::acceptedAndSentForReviewOrBelongsToAuthUser();

		$resource = (new CommentSearchResource(request(), $builder));

		return $resource->view();
	}

	public function before()
	{
		$this->request = request();

		$this->input = $this->request->all(['search_str', 'order', 'per_page']);
	}

	/**
	 * Комментарии книг автора
	 *
	 * @param Author $author
	 * @return View
	 */
	function author_books(Author $author)
	{
		$this->before();

		$books_ids = $author->any_books()
			->select('id')
			->pluck('id')
			->toArray();

		if (count($books_ids) < 1) $books_ids = [];

		$builder = Comment::whereIn('commentable_id', $books_ids)
			->acceptedAndSentForReviewOrBelongsToAuthUser();

		$resource = (new CommentSearchResource(request(), $builder));

		return $resource->view();
	}

	/**
	 * Комментарии книг серии
	 *
	 * @param Sequence $sequence
	 * @return View
	 */
	function sequence_books(Sequence $sequence)
	{
		$this->before();

		$books_ids = $sequence->books()
			->select('id')
			->pluck('id')
			->toArray();

		if (count($books_ids) < 1) $books_ids = [];

		$builder = Comment::whereIn('commentable_id', $books_ids)
			->acceptedAndSentForReviewOrBelongsToAuthUser();

		$resource = (new CommentSearchResource(request(), $builder));

		return $resource->view();
	}

	/**
	 * Комментарии пользователя
	 *
	 * @param User $user
	 * @return View
	 */
	function user(User $user)
	{
		$this->before();

		$query = $user->comments()
			->acceptedAndSentForReviewOrBelongsToAuthUser();

		$resource = (new CommentSearchResource(request(), $query));

		return $resource->view();
	}

	/**
	 * Комментарии подписок и подписчиков
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	function subscriptions(User $user)
	{
		$this->authorize('view_subscription_comments', $user);

		$this->before();

		$builder = Comment::whereExists(function ($query) use ($user) {
			$query->select(DB::raw(1))
				->from('user_relations')
				->whereRaw('comments.create_user_id = user_relations.user_id2')
				->where('user_id', $user->id)
				->whereIn('status', [UserRelationType::Friend, UserRelationType::Subscriber]);
		})->acceptedAndSentForReviewOrBelongsToAuthUser();

		$resource = (new CommentSearchResource(request(), $builder));

		return $resource->view();
	}

	/**
	 * Комментарии на прочитанные книги
	 *
	 * @param User $user
	 * @return View
	 */
	function user_readed_books(User $user)
	{
		$this->before();
		/*
				$builder = Comment::void()
					->bookType()
					->whereHas('book.users_read_statuses', function ($query) use ($user) {
						$query->where('status', 'readed')
							->where('user_id', $user->id);
					});
		*/
		$builder = Comment::void()
			->acceptedAndSentForReviewOrBelongsToAuthUser()
			->whereHasMorph('commentable',
				['App\Book'],
				function (Builder $query) use ($user) {
					$query->whereHas('users_read_statuses', function ($query) use ($user) {
						$query->where('status', 'readed')
							->where('user_id', $user->id);
					});
				});
		/*
			->whereHas('commentable', function ($query) use ($user) {
				$query->has('users_read_statuses', function ($query) use ($user) {
					$query->where('status', 'readed')
						->where('user_id', $user->id);
				});
			});
			*/

		$resource = (new CommentSearchResource(request(), $builder));

		return $resource->view();
	}

	function finaly()
	{
		$this->query->with([
			'originCommentable.authors.managers',
			"create_user.avatar",
			'create_user.latest_user_achievements',
			"create_user.groups",
			"create_user.relationship",
		]);

		if ($this->input['search_str']) {
			$this->query->fulltextSearch($this->input['search_str']);
		}

		$this->order();

		$comments = $this->query->simplePaginate(config('litlife.comments_on_page_count'));

		$comments->loadMissing([
			'votes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			}
		]);

		$comments->loadMissing(['commentable.votes' => function ($query) use ($comments) {
			$query->whereIn('create_user_id', $comments->pluck('create_user.id', 'id')->unique()->toArray());
		}]);

		$array = [
			'input' => $this->input,
			'comments' => $comments,
			'item_render' => $this->item_render,
			'order_array' => $this->order_array
		];

		if ($this->request->ajax()) {

			if ($this->request->input("with_panel") == 'true') {
				return view('comment.search', $array)->render();
			}

			return view('comment.list', $array)->render();
		}

		return view('comment.search_with_full_content', $array)->render();
	}

	function order()
	{
		$this->order_array['DateDesc'] = function () {
			$this->query->orderBy('created_at', 'desc');
		};

		$this->order_array['DateAsc'] = function () {
			$this->query->orderBy('created_at', 'asc');
		};

		$this->order_array['VoteDesc'] = function () {
			$this->query->orderBy('vote', 'desc');
		};

		$this->order_array['VoteAsc'] = function () {
			$this->query->orderBy('vote', 'asc');
		};

		$order = $this->input['order'];

		if (array_key_exists($order, $this->order_array))
			$this->order_array[$order]();
		else
			// если сортировка не указана то присваиваем сортировку по умолчанию
			$this->order_array[$this->defaultSorting]();
	}
}
