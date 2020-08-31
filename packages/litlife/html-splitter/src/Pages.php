<?php

namespace Litlife\HtmlSplitter;

use Iterator;

class Pages implements Iterator
{
	private $pages = [];
	private $first_page_number;
	private $page_number;

	public function __construct($first_page_number = 1)
	{
		$this->first_page_number = $first_page_number;
		$this->page_number = $first_page_number;
	}

	public function currentPage()
	{
		if (empty($this->pages[$this->page_number]))
			$this->pages[$this->page_number] = new Page();

		return $this->pages[$this->page_number];
	}

	public function toArray()
	{
		return $this->pages;
	}

	public function divide()
	{
		$this->next();
	}

	public function next()
	{
		++$this->page_number;
	}

	public function page($number)
	{
		if (isset($this->pages[$number]))
			return $this->pages[$number];
		else
			return null;
	}

	public function count(): int
	{
		return count($this->pages);
	}

	public function last()
	{
		$latest_page_number = $this->getLatestPageNumber();

		return $this->pages[$latest_page_number];
	}

	public function getLatestPageNumber(): int
	{
		return max(array_keys($this->pages));
	}

	public function getAllPagesCharactersCount(): int
	{
		$count = 0;

		foreach ($this->pages as $page) {
			$count += $page->getCharactersCount();
		}

		return $count;
	}

	public function delete($number)
	{
		unset($this->pages[$number]);
	}

	public function rewind()
	{
		$this->page_number = $this->first_page_number;
	}

	public function current()
	{

		if (empty($this->pages[$this->page_number]))
			$this->pages[$this->page_number] = new Page();

		return $this->pages[$this->page_number];
	}

	public function key()
	{
		return $this->page_number;
	}

	public function valid()
	{

		return isset($this->pages[$this->page_number]);
	}
}
