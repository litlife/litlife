<?php

namespace App\Http\Controllers;

use App\Forum;
use App\Post;
use App\Topic;
use App\User;
use Illuminate\View\View;

class PostListController extends Controller
{
	public $input;
	public $query;
	public $item_render = 'default';
	public $defaultSorting = 'DateDesc';
	public $limit = 20;
	private $request;
	private $order_array;

	/**
	 * Список сообщений
	 *
	 * @return View
	 */

	function index()
	{
		$this->before();

		$this->query = Post::void();

		return $this->finaly();
	}

	protected function before()
	{
		$this->request = request();

		$this->input = $this->request->all(['search_str', 'order']);
	}

	function finaly()
	{
		$this->query->with(["create_user.avatar", 'topic', 'forum']);

		if ($this->input['search_str']) {
			$this->query->fulltextSearch($this->input['search_str']);
		}

		/*
  SELECT
  "posts".*,
  "topics"."id",
  "topics"."forum_id",
  "users_access_to_forums".*,
  "forums"."private"
  FROM "posts"
  INNER JOIN "topics" ON "topics"."id" = "posts"."topic_id"
  INNER JOIN "forums" ON "forums"."id" = "topics"."forum_id"
  LEFT JOIN "users_access_to_forums"
	ON "users_access_to_forums"."forum_id" = "topics"."forum_id" AND "users_access_to_forums"."user_id" = '50001'

  WHERE "posts"."create_user_id" = '83777' AND "posts"."create_user_id" IS NOT NULL AND "posts"."deleted_at" IS NULL
  and ("forums"."private" is false or ("forums"."private" is true and "users_access_to_forums"."user_id" IS not null))
  ORDER BY "created_at" DESC
  */
		/*
				$this->query->select('posts.*', 'topics.id', 'topics.forum_id', 'users_access_to_forums.*', 'forums.private');

				$this->query->join('topics', 'topics.id', '=', 'posts.topic_id')
					->join('forums', 'forums.id', '=', 'topics.forum_id')
					->leftJoin('users_access_to_forums', function ($join) {
						$join->on('users_access_to_forums.forum_id', '=', 'topics.forum_id')
							->where('users_access_to_forums.user_id', auth()->id());
					});
		*/

		$this->query->select('posts.*', 'users_access_to_forums.user_id', 'forums.private');

		$this->query->withUserAccessToForums();

		$this->order();

		$posts = $this->query->simplePaginate();

		$posts->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);


		$array = [
			'input' => $this->input,
			'posts' => $posts,
			'item_render' => $this->item_render,
			'order_array' => $this->order_array
		];

		if ($this->request->ajax()) {

			if ($this->request->input("with_panel") == 'true') {
				return view('forum.post.search', $array)->render();
			}

			return view('forum.post.list', $array)->render();
		}

		return view('forum.post.search_with_full_content', $array)->render();
	}

	function order()
	{
		$this->order_array['DateDesc'] = function () {
			$this->query->orderBy('created_at', 'desc');
		};

		$this->order_array['DateAsc'] = function () {
			$this->query->orderBy('created_at', 'asc');
		};

		$this->order_array['like_desc'] = function () {
			$this->query->orderBy('like_count', 'desc');
		};

		$order = $this->input['order'];

		if (array_key_exists($order, $this->order_array))
			$this->order_array[$order]();
		else
			// если сортировка не указана то присваиваем сортировку по умолчанию
			$this->order_array[$this->defaultSorting]();
	}

	/**
	 * Сообщения пользователя
	 *
	 * @return View
	 */

	function user(User $user)
	{
		$this->before();

		$this->query = $user->posts();

		return $this->finaly();
	}

	/**
	 * Сообщения темы
	 *
	 * @param Topic $topic
	 * @return View
	 */

	function topic(Topic $topic)
	{
		$this->before();

		$this->query = $topic->posts();

		return $this->finaly();
	}

	/**
	 * Сообщения форума
	 *
	 * @param Forum $forum
	 * @return View
	 */

	function forum(Forum $forum)
	{
		$this->before();

		$this->query = $forum->posts();

		return $this->finaly();
	}
}

