<?php

namespace App\Library;

use App\Book;
use App\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommentSearchResource extends SearchResource
{
	public $defaultSorting = 'DateDesc';
	public $simple_paginate = true;
	public $view_name = 'comment.list.default';

	public function renderAjax($vars)
	{
		return view('comment.list.' . $this->view_name, $vars);
	}

	public function view()
	{
		$vars = $this->getVars();

		if ($this->simple_paginate)
			$vars['comments'] = $this->query->simplePaginate();
		else
			$vars['comments'] = $this->query->paginate();

		$comments = $vars['comments'];

		if ($this->request->ajax())
			return view('comment.list', $vars);

		return view('comment.search_with_full_content', $vars);
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
		$this->input = $this->request->all(['search_str', 'order', 'per_page']);

		if ($this->input['search_str'])
			$this->query->fulltextSearch($this->input['search_str']);

		$this->query->with([
			"create_user.avatar",
			'create_user.latest_user_achievements',
			"create_user.groups",
			"userBookVote",
			'votes' => function ($query) {
				$query->where('create_user_id', auth()->id());
			},
			'originCommentable' => function (MorphTo $morphTo) {
				$morphTo->morphWith([
					Book::class => [
						'authors.managers'
					],
					Collection::class => [
						'collectionUser'
					]
				]);
			}]);

		$this->order();

		return $this;
	}

	public function order()
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

		return $this;
	}

	public function setSimplePaginate($set)
	{
		$this->simple_paginate = $set;

		return $this;
	}
}