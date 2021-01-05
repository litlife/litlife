<?php

namespace App\Http\SearchResource;

use App\Enums\Gender;
use App\Library\SearchResource;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;

class SequenceSearchResource extends SearchResource
{
	public $defaultSorting = 'name_asc';
	public $simple_paginate = true;
	public $limit = 20;
	public $view_name = 'sequence.list.default';

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
			$vars['sequences'] = $this->query->simplePaginate();
		else
			$vars['sequences'] = $this->query->paginate();

        SEOMeta::setDescription(implode(', ', $vars['sequences']->pluck('name')->toArray()));

		if ($this->request->ajax())
			return view('sequence.list', $vars);

		return view('sequence.search', $vars);
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

        $this->input = $this->request->all(['search', 'order', 'limit', 'view', 'per_page']);

        if ($this->input['search']) {
            $this->query->fulltextSearch($this->input['search']);
        }

        if (isset($this->input['limit']))
            $this->limit = $this->input['limit'];
        else
            $this->limit = 50;

        $this->order();

		return $this;
	}

	public function order()
	{
        $this->order_array['book_count_desc'] = function () {
            $this->query->orderByBooksCountAsc();
        };

        $this->order_array['book_count_asc'] = function () {
            $this->query->orderByBooksCountDesc();
        };

        $this->order_array['name_asc'] = function () {
            $this->query->orderBy('name', 'asc')
                ->orderBy('sequences.id', 'asc');
        };

        $this->order_array['name_desc'] = function () {
            $this->query->orderBy('name', 'desc')
                ->orderBy('sequences.id', 'asc');
        };

        $this->order_array['latest'] = function () {
            $this->query->latest();
        };

        $this->order_array['oldest'] = function () {
            $this->query->oldest();
        };

		return $this;
	}

	public function setSimplePaginate($set)
	{
		$this->simple_paginate = $set;

		return $this;
	}
}