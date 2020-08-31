<?php

namespace App\Http\Controllers;

use App\Topic;
use App\User;
use Illuminate\View\View;

class TopicListController extends Controller
{
	public $input;
	public $query;
	public $item_render = 'default';
	public $defaultSorting = 'DateDesc';
	public $limit = 20;
	private $order_array;
	private $request;

	/**
	 * Список всех тем
	 *
	 * @param
	 * @return View
	 */

	function index()
	{
		$this->before();

		$this->query = Topic::void();

		return $this->finaly();
	}

	function before()
	{
		$this->request = request();

		$this->input = $this->request->all(['search_str', 'order']);
	}

	function finaly()
	{
		$this->query->with(["last_post.create_user", "forum"]);

		if ($this->input['search_str']) {
			$this->query->fulltextSearch($this->input['search_str']);
		}

		$this->query->select('topics.*')
			->withUserAccessToForums();

		$this->order();

		$topics = $this->query->simplePaginate();

		$array = [
			'input' => $this->input,
			'topics' => $topics,
			'item_render' => $this->item_render,
			'order_array' => $this->order_array
		];

		if ($this->request->ajax()) {

			return view('forum.topic.list', $array)->render();
		}

		return view('forum.topic.search', $array)->render();
	}

	function order()
	{
		$this->order_array['DateDesc'] = function () {
			$this->query->orderBy('created_at', 'desc');
		};

		$this->order_array['DateAsc'] = function () {
			$this->query->orderBy('created_at', 'asc');
		};

		$this->order_array['last_post_created_at_desc'] = function () {
			$this->query->orderByLastPostDescNullsLast();
		};

		$order = $this->input['order'];

		if (array_key_exists($order, $this->order_array))
			$this->order_array[$order]();
		else
			// если сортировка не указана то присваиваем сортировку по умолчанию
			$this->order_array[$this->defaultSorting]();

	}

	/**
	 * Список тем пользователя
	 *
	 * @param User $user
	 * @return View
	 */

	function user(User $user)
	{
		$this->before();

		$this->query = $user->topics();

		return $this->finaly();
	}
}
