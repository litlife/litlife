<?php

namespace App\Http\SearchResource;

use App\Enums\Gender;
use App\Library\SearchResource;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;

class AuthorSearchResource extends SearchResource
{
	public $defaultSorting = 'rating';
	public $simple_paginate = true;
	public $limit = 20;
	public $view_name = 'author.list.default';

	public function __construct(Request $request, $query)
	{
		parent::__construct($request, $query);
	}

	public function renderAjax($vars)
	{
		return view($this->getViewName(), $vars);
	}

	public function view()
	{
		$vars = $this->getVars();

		if ($this->simple_paginate)
			$vars['authors'] = $this->query->simplePaginate();
		else
			$vars['authors'] = $this->query->paginate();

		SEOMeta::setDescription(implode(', ', $vars['authors']->pluck('name')->toArray()));

		if ($this->request->ajax())
			return view($this->getViewName(), $vars);

		return view('author.search', $vars);
	}

	public function getVars()
	{
		$this->searchParameters();

		$this->selectDefaultOrder();

		$this->vars = array_merge($this->vars, [
			'resource' => $this,
			'input' => $this->input,
			'order_array' => $this->order_array
		]);

		return $this->vars;
	}

	public function searchParameters()
	{
		$this->request = request();

		$this->input = $this->request->all(['search', 'first_name', 'gender', 'Biography', 'Photo',
			'last_name', 'nick', 'moderator', 'middle_name', 'email', 'lang', 'order', 'limit', 'view', 'per_page']);

		if (!in_array($this->input['view'], ['table', 'gallery'])) {
			if (in_array(session('authors_search_view'), ['table', 'gallery'])) {
				$this->input['view'] = $this->request->session()->get('authors_search_view');
			} else {
				$this->input['view'] = 'table';
			}
		}

		$this->request->session()->put('authors_search_view', $this->input['view']);

		$this->query->select("authors.*");

		if ($this->input['search']) {
			if (mb_strlen($this->input['search']) <= 2)
				//$this->query->similaritySearch($this->input['search']);
				$this->query->where('name_helper', 'ILIKE', '' . $this->input['search'] . '%');
			//$this->query->whereRaw('"name_helper" ~* ?', $this->input['search']);
			else
				$this->query->fulltextSearch($this->input['search']);
			//$this->query->wordSimilaritySearch($this->input['search']);

		} else {
			if ($this->input['first_name'])
				$this->query->where("first_name", 'ilike', $this->input['first_name'] . '%');

			if ($this->input['last_name'])
				$this->query->where("last_name", 'ilike', $this->input['last_name'] . '%');

			if ($this->input['middle_name'])
				$this->query->where("middle_name", 'ilike', $this->input['middle_name'] . '%');

			if ($this->input['nick'])
				$this->query->where("nickname", 'ilike', $this->input['nick'] . '%');
		}

		if ($this->input['gender'] and Gender::hasKey($this->input['gender']))
			$this->query->where("gender", '=', $this->input['gender']);


		switch ($this->input['Photo']) {
			case 'enable':
				$this->query->whereNotNull('photo_id');
				break;

			case 'disable':
				$this->query->whereNull('photo_id');
				break;
		}

		switch ($this->input['Biography']) {
			case 'enable':
				$this->query->whereNotNull('biography_id');
				break;

			case 'disable':
				$this->query->whereNull('biography_id');
				break;
		}

		if ($this->input['lang']) {
			if ($this->input['lang'] == 'not_specified')
				$this->query->whereNull("lang");
			else
				$this->query->where("lang", $this->input['lang']);
		}

		if (isset($this->input['limit']))
			$limit = $this->input['limit'];
		else
			$limit = 36;

		$this->order();

		$this->query->with('photo', 'managers.user');

		return $this;
	}

	public function order()
	{
		$this->order_array['rating'] = function () {
			$this->query->orderByRating();
		};

		$this->order_array['ln'] = function () {
			$this->query->orderBy("last_name", 'asc')
				->orderBy("first_name", 'asc')
				->orderBy("middle_name", 'asc')
				->where("last_name", "!=", "");
		};

		$this->order_array['fn'] = function () {
			$this->query->orderBy("last_name", 'asc')
				->orderBy("first_name", 'asc')
				->orderBy("middle_name", 'asc')
				->where("first_name", "!=", "");
		};

		$this->order_array['mn'] = function () {
			$this->query->orderBy("last_name", 'asc')
				->orderBy("first_name", 'asc')
				->orderBy("middle_name", 'asc')
				->where("middle_name", "!=", "");
		};

		$this->order_array['book_volume'] = function () {
			$this->query->orderBy("books_count", 'desc');
		};

		$this->order_array['created_at_asc'] = function () {
			$this->query->orderByWithNulls('created_at', 'asc', 'first');
		};

		$this->order_array['created_at_desc'] = function () {
			$this->query->orderByWithNulls('created_at', 'desc', 'last');
		};

		$this->order_array['rating_week_desc'] = function () {
			$this->query->orderByRatingWeekDesc();
		};

		return $this;
	}

	public function setSimplePaginate($set)
	{
		$this->simple_paginate = $set;

		return $this;
	}
}