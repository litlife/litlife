<?php

namespace App\Http\SearchResource;

use App\Library\SearchResource;

class BlogPostSearchResource extends SearchResource
{
	public $defaultSorting = 'created_at_desc';
	public $simple_paginate = true;
	public $view_name = 'blog.item';

	public function renderAjax($vars)
	{
		return view('blog.list.' . $this->view_name, $vars);
	}

	public function view()
	{
		$vars = $this->getVars();

		if ($this->simple_paginate)
			$vars['blogs'] = $this->query->simplePaginate();
		else
			$vars['blogs'] = $this->query->paginate();

		$blogs = $vars['blogs'];

		if ($this->request->ajax())
			return view('blog.list', $vars);

		return view('blog.search', $vars);
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
			'likes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			}
		]);

		$this->order();

		return $this;
	}

	public function order()
	{
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