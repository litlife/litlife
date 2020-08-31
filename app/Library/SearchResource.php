<?php

namespace App\Library;

use Illuminate\Http\Request;

class SearchResource
{
	public $input = [];
	public $query;
	public $view = 'default';
	public $view_name = 'default';
	public $request;
	public $order_array = [];
	public $vars = [];
	public $disabled_filters = [];
	public $limit = 20;
	public $defaultInputValueArray = [];

	public function __construct(Request $request, $query)
	{
		$this->query = $query;
		$this->request = $request;
	}

	public function getVars()
	{
		return $this->vars;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function with($key, $value)
	{
		$this->vars[$key] = $value;

		return $this;
	}

	public function defaultSorting($sort)
	{
		$this->defaultSorting = $sort;

		return $this;
	}

	public function addOrder($name, $callback)
	{
		$this->order_array[$name] = $callback;

		return $this;
	}

	public function setViewType($name)
	{
		$this->view_name = $name;

		return $this;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function selectDefaultOrder()
	{
		$order = $this->input['order'];

		if (array_key_exists($order, $this->order_array)) {
			$this->order_array[$order]($this->query);
		} else {
			// если сортировка не указана то присваиваем сортировку по умолчанию
			$this->order_array[$this->defaultSorting]($this->query);
			$this->input['order'] = $this->defaultSorting;
		}
	}

	public function disableFilter($filter)
	{
		$this->disabled_filters[] = $filter;

		return $this;
	}

	public function ifDisabledFilter($filter)
	{
		if (in_array($filter, $this->disabled_filters))
			return true;
		else
			return false;
	}

	public function ifFilterEnabled($filter)
	{
		if (!in_array($filter, $this->disabled_filters))
			return true;
		else
			return false;
	}

	public function setDefaultInputValue($key, $value)
	{
		$this->defaultInputValueArray[$key] = $value;

		return $this;
	}

	public function getDefaultInputValue($key)
	{
		return $this->defaultInputValueArray[$key];
	}

	public function setInputValue($key, $value)
	{
		$this->input[$key] = $value;

		return $this;
	}

	public function getInputValue($key)
	{
		if (!isset($this->input[$key])) {
			if (isset($this->defaultInputValueArray[$key])) {
				return $this->defaultInputValueArray[$key];
			}
		}

		return $this->input[$key];
	}

	public function getViewName()
	{
		return $this->view_name;
	}
}