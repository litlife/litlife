<?php

namespace App\Http\Controllers;

use App\Sequence;
use App\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\View\View;

class SequenceListController extends Controller
{
	public $input;
	public $query;
	public $item_render = 'default';
	public $defaultSorting = 'name_asc';
	public $order_array;
	public $request;
	public $simple_paginate = true;
	public $limit = 20;

	/**
	 * Список серий
	 *
	 * @return View
	 */
	function index()
	{
		$this->before();

		$this->query = Sequence::notMerged()
			->acceptedOrBelongsToAuthUser();

		return $this->finaly();
	}

	public function before()
	{
		$this->request = request();
		$this->input = $this->request->all(['search', 'order']);
	}

	function finaly()
	{
		if ($this->input['search']) {
			$this->query->fulltextSearch($this->input['search']);
		}

		if (isset($this->input['limit']))
			$this->limit = $this->input['limit'];
		else
			$this->limit = 50;

		$this->order();

		if ($this->simple_paginate)
			$sequences = $this->query->simplePaginate();
		else
			$sequences = $this->query->paginate();

		SEOMeta::setDescription(implode(', ', $sequences->pluck('name')->toArray()));


		if (request()->ajax()) {

			return view('sequence.list', [
				'input' => $this->input,
				'sequences' => $sequences,
				'item_render' => $this->item_render
			])->render();
		}

		return view('sequence.search', [
			'input' => $this->input,
			'sequences' => $sequences,
			'item_render' => $this->item_render,
			'order_array' => $this->order_array
		]);
	}

	function order()
	{
		$this->order_array['book_count_desc'] = function () {
			$this->query->orderByBooksCountAsc();
		};

		$this->order_array['book_count_asc'] = function () {
			$this->query->orderByBooksCountDesc();
		};

		$this->order_array['name_desc'] = function () {
			$this->query->orderBy('name', 'desc')
				->orderBy('sequences.id', 'asc');
		};

		$this->order_array['name_asc'] = function () {
			$this->query->orderBy('name', 'asc')
				->orderBy('sequences.id', 'asc');
		};

		$this->order_array['latest'] = function () {
			$this->query->latest();
		};

		$this->order_array['oldest'] = function () {
			$this->query->oldest();
		};

		$order = $this->input['order'];

		if (array_key_exists($order, $this->order_array))
			$this->order_array[$order]();
		else
			// если сортировка не указана то присваиваем сортировку по умолчанию
			$this->order_array[$this->defaultSorting]();
	}

	/**
	 * Серии пользователя
	 *
	 * @param User $user
	 * @return View
	 */
	public function userCreated(User $user)
	{
		$this->before();

		$this->query = $user->created_sequences()->withoutCheckedScope();

		$this->defaultSorting = 'latest';

		$this->simple_paginate = false;

		return $this->finaly();
	}

	/**
	 * Серии в библиотеке пользователя
	 *
	 * @param User $user
	 * @return View
	 */
	function userLibrary(User $user)
	{
		$this->before();

		$this->query = $user->sequences()
			->withPivot('created_at')
			->any();
		/*
				$this->query->join('user_sequences', function ($join) use ($user) {
					$join->on('sequences.id', '=', 'user_sequences.sequence_id')
						->where('user_sequences.user_id', '=', $user->id);
				});
				*/

		$this->order_array['user_sequences_created_at_asc'] = function () {
			$this->query->orderBy('pivot_created_at', 'asc')
				->orderBy('sequences.id', 'desc');
		};

		$this->order_array['user_sequences_created_at_desc'] = function () {
			$this->query->orderBy('pivot_created_at', 'desc')
				->orderBy('sequences.id', 'asc');
		};

		$this->simple_paginate = false;

		$this->defaultSorting = 'user_sequences_created_at_asc';

		return $this->finaly();
	}
}
