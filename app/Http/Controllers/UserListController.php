<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\Comment;
use App\Enums\Gender;
use App\Enums\ReadStatus;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\View\View;

class UserListController extends Controller
{
	public $input;
	public $query;
	public $item_render = 'default';
	public $order_array;
	public $defaultSorting = 'forum_post_count_desc';
	public $request;
	public $limit = 20;

	/**
	 * Привлеченные пользователи
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	function referredUsers(User $user)
	{
		$this->before();

		$this->authorize('view_referred_users', $user);

		$this->query = $user->refered_users();

		$this->item_render = 'referred_user';

		return $this->finaly();
	}

	protected function before()
	{
		$this->request = request();

		$this->input = $this->request->all(['search', 'first_name', 'last_name', 'nick', 'middle_name', 'gender',
			'email', 'limit', 'order', 'is_online', 'text_status', 'group', 'with_photo', 'birth_day', 'birth_month', 'birth_year', 'per_page']);
	}

	function finaly()
	{
		if ($this->input['search']) {
			$this->query->fulltextSearch($this->input['search']);
		} else {
			if ($this->input['first_name'])
				$this->query->where("first_name", 'ilike', $this->input['first_name'] . '%');

			if ($this->input['last_name'])
				$this->query->where("last_name", 'ilike', $this->input['last_name'] . '%');

			if ($this->input['nick'])
				$this->query->where("nick", 'ilike', $this->input['nick'] . '%');
		}

		if ($this->input['gender'] and Gender::hasKey($this->input['gender']))
			$this->query->where("gender", '=', Gender::getValue($this->input['gender']));

		if ($this->input['is_online']) {

			$this->query->online();
		}

		if ($this->input['text_status']) {

			$this->query->where("text_status", $this->input['text_status']);
		}

		if ($this->input['with_photo']) {

			$this->query->whereNotNull("avatar_id");
		}

		if ((int)$this->input['group']) {

			$this->query->whereHas('groups', function ($query) {
				$query->where('id', $this->input['group'])
					->select('user_groups.id');
			});
		}

		if ((int)$this->input['birth_day']) {
			$this->query->whereRaw("date_part('day', \"born_date\") = ?", [intval($this->input['birth_day'])]);
		}
		if ((int)$this->input['birth_month']) {

			$this->query->whereRaw("date_part('month', \"born_date\") = ?", [intval($this->input['birth_month'])]);
		}
		if ((int)$this->input['birth_year']) {

			$this->query->whereRaw("date_part('year', \"born_date\") = ?", [intval($this->input['birth_year'])]);
		}

		//WHERE date_part('month', "born_date") = '2' and date_part('day', "born_date") = '3'

		if ($this->input['email']) {
			/*
						$this->query->join('user_emails', function ($join) {
							$join->on('users.id', '=', 'user_emails.user_id')
								->where('user_emails.email', $this->input['email'])
								->where('user_emails.confirm', true)
								->whereNull('user_emails.deleted_at');
						});
						*/
			$this->query->whereHas('emails', function ($query) {
				$query->whereEmail($this->input['email'])
					->confirmed();
			});
		}

		$this->query->with(['avatar', 'groups'])
			->with('latest_user_achievements');

		$this->order();

		$users = $this->query->simplePaginate();

		$array = [
			'input' => $this->input,
			'users' => $users,
			'item_render' => $this->item_render,
			'order_array' => $this->order_array
		];

		if ($this->request->ajax()) {

			if ($this->request->input("with_panel") == 'true') {
				return view('user.search', $array)->render();
			}

			return view('user.list', $array)->render();
		}

		return view('user.search_with_full_content', $array);
	}

	protected function order()
	{
		$this->order_array['created_at_desc'] = function () {
			$this->query->orderBy('users.created_at', 'desc');
		};

		$this->order_array['created_at_asc'] = function () {
			$this->query->orderBy('users.created_at', 'asc');
		};

		$this->order_array['WithPhotoFirst'] = function () {
			$this->query->orderByRaw('"avatar_id" desc');
		};

		$this->order_array['LoginDateDown'] = function () {
			$this->query->orderBy('last_activity_at', 'desc');
		};

		$this->order_array['LoginDateUp'] = function () {
			$this->query->orderBy('last_activity_at', 'asc');
		};

		$this->order_array['forum_post_count_desc'] = function () {
			$this->query->orderByPostsCountDesc();
		};

		$this->order_array['forum_post_count_asc'] = function () {
			$this->query->orderBy('forum_message_count', 'asc');
		};

		$this->order_array['comment_count_desc'] = function () {
			$this->query->orderBy('comment_count', 'desc');
		};

		$this->order_array['comment_count_asc'] = function () {
			$this->query->orderBy('comment_count', 'asc');
		};

		/*
		$order = $this->input['order'];

		if (array_key_exists($order, $this->order_array))
			$this->order_array[$order]();
		else
			// если сортировка не указана то присваиваем сортировку по умолчанию
			$this->order_array[$this->defaultSorting]();
		*/

		if (!array_key_exists($this->input['order'], $this->order_array))
			$this->input['order'] = $this->defaultSorting;

		$order = $this->input['order'];

		$this->order_array[$order]();
	}

	/**
	 * Друзья
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */
	function friends(User $user)
	{
		$this->before();

		$this->authorize('view_relations', $user);

		$this->query = $user->friends();

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderBy('pivot_user_updated_at', 'desc');
		};

		$this->defaultSorting = 'user_updated_at_desc';

		$this->item_render = 'default';

		return $this->finaly();
	}

	/**
	 * Список
	 *
	 * @return View
	 */

	function index()
	{
		$this->before();

		$this->query = User::void();

		$this->query->active();

		return $this->finaly();
	}

	/**
	 * Кто лайкнул
	 *
	 * @param string $type
	 * @param int $id
	 * @return View
	 */

	function whoLikes($type, $id)
	{
		$this->before();

		$map = Relation::morphMap();

		if (!isset($map[$type]))
			abort(404);
		else
			$model = $map[$type];

		$item = $model::any()->find($id);

		if (empty($item))
			abort(404);

		$this->query = User::void()
			->join('likes', function ($join) use ($type, $item) {
				$join->on('users.id', '=', 'likes.create_user_id')
					->where('likeable_type', $type)
					->where('likeable_id', $item->id)
					->whereNull('likes.deleted_at');
			})->select('users.*', 'likes.created_at as likes_created_at', 'likes.ip as likes_ip');

		$this->item_render = 'like';

		$this->order_array['last_likes'] = function () {
			$this->query->orderBy('likes_created_at', 'desc');
		};

		$this->order_array['first_likes'] = function () {
			$this->query->orderBy('likes_created_at', 'asc');
		};

		$this->defaultSorting = 'last_likes';

		return $this->finaly();
	}

	/**
	 * Подписки
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */

	function subscriptions(User $user)
	{
		$this->before();

		$this->authorize('view_relations', $user);

		$this->query = $user->subscriptions();

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderBy('pivot_user_updated_at', 'desc');
		};

		$this->defaultSorting = 'user_updated_at_desc';

		return $this->finaly();
	}

	/**
	 * Подписчики
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */

	function subscribers(User $user)
	{
		$this->before();

		$this->authorize('view_relations', $user);

		$this->query = $user->subscribers();

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderBy('pivot_user_updated_at', 'desc');
		};

		$this->defaultSorting = 'user_updated_at_desc';

		return $this->finaly();
	}

	/**
	 * Пользователи в черном списке
	 *
	 * @param User $user
	 * @return View
	 * @throws
	 */

	function blacklists(User $user)
	{
		$this->before();

		$this->authorize('view_users_in_blacklist', $user);

		$this->query = $user->blacklists();

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderBy('pivot_user_updated_at', 'desc');
		};

		$this->defaultSorting = 'user_updated_at_desc';

		return $this->finaly();
	}

	/**
	 * Купившие книгу пользователи
	 *
	 * @param Book $book
	 * @return View
	 */
	public function boughtBook(Book $book)
	{
		$this->before();

		$this->query = $book->boughtUsers();

		$this->order_array['purchases_created_at_desc'] = function () {
			$this->query->orderBy('pivot_created_at', 'desc');
		};

		$this->defaultSorting = 'purchases_created_at_desc';

		return $this->finaly();
	}

	/**
	 * Прочитавшие книгу
	 *
	 * @param Book $book
	 * @return View
	 */
	function usersRead(Book $book)
	{
		$this->before();

		$this->query = $book->userStatuses()
			->where('status', ReadStatus::Readed);

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderByWithNulls('pivot_user_updated_at', 'desc', 'last');
		};

		$this->defaultSorting = 'user_updated_at_desc';
		$this->item_render = 'book_status';

		return $this->finaly();
	}

	/**
	 * Хотят прочитать
	 *
	 * @param Book $book
	 * @return View
	 */

	function usersWantToRead(Book $book)
	{
		$this->before();

		$this->query = $book->userStatuses()
			->where('status', ReadStatus::ReadLater);

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderByWithNulls('pivot_user_updated_at', 'desc', 'last');
		};

		$this->defaultSorting = 'user_updated_at_desc';
		$this->item_render = 'book_status';

		return $this->finaly();
	}

	/**
	 * Читают сейчас
	 *
	 * @param Book $book
	 * @return View
	 */

	function usersReadNow(Book $book)
	{
		$this->before();

		$this->query = $book->userStatuses()
			->where('status', ReadStatus::ReadNow);

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderByWithNulls('pivot_user_updated_at', 'desc', 'last');
		};

		$this->defaultSorting = 'user_updated_at_desc';
		$this->item_render = 'book_status';

		return $this->finaly();
	}

	/**
	 * Не дочитали
	 *
	 * @param Book $book
	 * @return View
	 */

	function usersCantRead(Book $book)
	{
		$this->before();

		$this->query = $book->userStatuses()
			->where('status', ReadStatus::ReadNotComplete);

		$this->order_array['user_updated_at_desc'] = function () {
			$this->query->orderByWithNulls('pivot_user_updated_at', 'desc', 'last');
		};

		$this->defaultSorting = 'user_updated_at_desc';
		$this->item_render = 'book_status';

		return $this->finaly();
	}

	/**
	 * Оценившие книги
	 *
	 * @param Book $book
	 * @return View
	 */
	function usersBookVotes(Book $book)
	{
		$this->before();

		if ($book->isInGroup() and $book->isNotMainInGroup()) {
			$this->query = $book->mainBook->votesUsers();
		} else {
			$this->query = $book->votesUsers();
		}

		$this->query->withPivot('user_updated_at');

		$this->order_array['book_votes_updated_at_desc'] = function () {
			$this->query->orderBy('book_votes.created_at', 'desc');
		};

		$this->defaultSorting = 'book_votes_updated_at_desc';

		$this->item_render = 'book_rate';

		return $this->finaly();
	}

	/**
	 * Кому понравился комментарий
	 *
	 * @param Comment $comment
	 * @return View
	 * @throws AuthorizationException
	 */

	function usersWhoLikesComment(Comment $comment)
	{
		$this->before();

		$this->authorize('viewWhoLikesOrDislikes', $comment);

		$this->query = User::void()
			->join("comment_votes", "comment_votes.create_user_id", "=", "users.id")
			->where("comment_id", $comment->id)
			->where("vote", ">", "0")
			->select("users.*", "comment_votes.vote");

		return $this->finaly();
	}

	/**
	 * Кому не понравился комментарий
	 *
	 * @param Comment $comment
	 * @return View
	 * @throws AuthorizationException
	 */

	function usersWhoDislikesComment(Comment $comment)
	{
		$this->before();

		$this->authorize('viewWhoLikesOrDislikes', $comment);

		$this->query = User::void()
			->join("comment_votes", "comment_votes.create_user_id", "=", "users.id")
			->where("comment_id", $comment->id)
			->where("vote", "<", "0")
			->select("users.*", "comment_votes.vote");

		return $this->finaly();
	}

	/**
	 * На модерации
	 *
	 * @return View
	 */

	function usersOnModeration()
	{
		$this->before();

		$this->query = User::void()
			->join("users_on_moderation", "users_on_moderation.user_id", "=", "users.id")
			->select("users.*", "users_on_moderation.user_adds_id");

		return $this->finaly();
	}

	/**
	 * Оценки книг автора
	 *
	 * @param Author $author
	 * @return View
	 */

	function author_books_votes(Author $author)
	{
		$this->before();

		$this->query = User::void()
			->join("book_votes", "book_votes.create_user_id", "=", "users.id")
			->join("book_authors", "book_authors.book_id", "=", "book_votes.book_id")
			->select("users.*", "book_votes.book_id", "book_votes.created_at", "book_votes.create_user_id", "book_votes.vote", "book_authors.book_id", "book_authors.author_id")
			->where("author_id", $author->id)
			//->orderBy("book_votes.created_at", "desc")
			->with("book");

		$this->order_array['book_votes_updated_at_asc'] = function () {
			$this->query->orderBy('book_votes.user_updated_at', 'asc');
		};

		$this->order_array['book_votes_updated_at_desc'] = function () {
			$this->query->orderBy('book_votes.user_updated_at', 'desc');
		};

		$this->defaultSorting = 'book_votes_updated_at_desc';


		$this->item_render = 'author_book_rate';


		return $this->finaly();
	}

	/**
	 * Приславшие сообщения
	 *
	 * @param User $user
	 * @return View
	 */

	function inbox(User $user)
	{
		$this->before();

		$this->authorize('view_inbox', $user);
		/*
		 * Придется использовать чистый sql так как возникают ошибки с биндингами
		 *
		$first = DB::table('message')
			->select(DB::raw('recepient_id, create_time, 0'))
			->where("sender_id", $user->id)
			->where("sender_del", false)
			->orderBy("create_time", "desc");

		$talks = DB::table('message')
			->select(DB::raw('sender_id, create_time, (CASE "is_read"  when true  then 0 when false then 1 end) as "new"'))
			->where("recepient_id", $user->id)
			->where("recepient_del", false)
			->union($first);
		*/
		/*
				$talksSql = 'SELECT
				 sender_id,
				 created_at,
				 "new"
			   FROM messages
			   WHERE recepient_id = ' . intval($user->id) . ' AND "recepient_del" IS FALSE

			   UNION

			   SELECT
				 recepient_id,
				 created_at,
				 0
			   FROM messages
			   WHERE sender_id = ' . intval($user->id) . ' AND "sender_del" = FALSE
			   ORDER BY created_at DESC';

				$this->query = User::select(DB::raw('"users".*, sender_id, MAX(talks.created_at) AS create_time, sum("new") AS new_messages'))
					->join(DB::raw("({$talksSql}) as talks"), 'users.id', '=', 'talks.sender_id')
					//->mergeBindings($talks)
					->groupBy("sender_id", "id");


				$this->query = $user->participations();


				$this->item_render = 'inbox';

				$this->order_array['last_personal_messages'] = function () {
					$this->query->orderBy('latest_message_id', 'desc');
				};

				$this->order_array['first_personal_messages'] = function () {
					$this->query->orderBy('latest_message_id', 'asc');
				};

				$this->defaultSorting = 'last_personal_messages';

				$this->limit = 16;

				return $this->finaly();
		*/
	}
}
