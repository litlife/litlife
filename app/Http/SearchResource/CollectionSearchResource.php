<?php

namespace App\Http\SearchResource;

use App\Library\SearchResource;

class CollectionSearchResource extends SearchResource
{
	public $defaultSorting = 'created_at_desc';
	public $simple_paginate = true;
	public $view_name = 'collection.item';

	public function renderAjax($vars)
	{
		return view('collection.list.' . $this->view_name, $vars);
	}

	public function view()
	{
		$vars = $this->getVars();

		if ($this->simple_paginate)
			$vars['collections'] = $this->query->simplePaginate();
		else
			$vars['collections'] = $this->query->paginate();

		$collections = $vars['collections'];

		if ($this->request->ajax())
			return view('collection.list', $vars);

		return view('collection.search', $vars);
	}

	public function getVars()
	{
		$this->searchParameters();

		$this->selectDefaultOrder();

		$this->vars = array_merge($this->vars, [
			'input' => $this->input,
			'view' => $this->view,
			'order_array' => $this->order_array,
			'disabled_filters' => $this->disabled_filters,
			'view_name' => $this->view_name
		]);

		return $this->vars;
	}

	public function searchParameters()
	{
		$this->input = $this->request->all(['search', 'order', 'per_page']);

		if ($this->input['search'])
			$this->query->fulltextSearch($this->input['search']);

		$this->query->with([
			'create_user.avatar',
			'latest_books.cover'
		]);

		$this->order();

		return $this;
	}

	public function order()
	{
		$this->order_array['likes_count_desc'] = function () {
			$this->query->orderByLikesCount();
		};

		$this->order_array['books_count_desc'] = function () {
			$this->query->orderByBooksCount();
		};

		$this->order_array['views_count_desc'] = function () {
			$this->query->orderByWithNulls('views_count', 'desc', 'last')
				->orderBy('id', 'desc');
		};

		$this->order_array['comments_count_desc'] = function () {
			$this->query->orderByWithNulls('comments_count', 'desc', 'last')
				->orderBy('id', 'desc');
		};

		$this->order_array['latest_updates'] = function () {
			$this->query->orderByWithNulls('latest_updates_at', 'desc', 'last');
		};

		$this->order_array['created_at_desc'] = function () {
			$this->query->orderBy('created_at', 'desc');
		};

		$this->order_array['created_at_asc'] = function () {
			$this->query->orderBy('created_at', 'asc');
		};

		return $this;
	}

	public function setSimplePaginate($set)
	{
		$this->simple_paginate = $set;

		return $this;
	}
}