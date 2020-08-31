<?php

namespace App\Traits;

trait PaginatableTrait
{
	/**
	 * @var int
	 */
	private $pageSizeLimit = 100;

	public function getPerPage()
	{
		$pageSize = intval(request('per_page', $this->perPage));

		if ($pageSize < 10)
			$pageSize = 10;

		return min($pageSize, $this->pageSizeLimit);
	}
}