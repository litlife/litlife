<?php

namespace App;


use App\Traits\PaginatableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use InvalidArgumentException;

abstract class Model extends Eloquent
{
	// use Cachable; Большой минус что этот пакет неправильно работает с global scope

	//use CacheQueryBuilder;
	use PaginatableTrait;

	// set default date Format
	public $dateFormat = 'Y-m-d H:i:s';

	public function scopeVoid($query)
	{
		return $query;
	}

	public function isChanged($attribute)
	{
		if (array_key_exists($attribute, $this->getChanges()))
			return true;
		elseif (array_key_exists($attribute, $this->getDirty()))
			return true;
		else
			return false;
	}

	public function scopeOrderByField($query, $column, $ids)
	{
		if (empty($column))
			throw new InvalidArgumentException('Column argument must be not null');

		if (empty($ids))
			throw new InvalidArgumentException('Ids array must be not empty');

		$count = 1;

		$qs = 'CASE ';

		$bindings = [];

		foreach ($ids as $id) {
			$qs .= 'WHEN "' . $column . '" = ? THEN ' . $count . ' ';
			array_push($bindings, $id);
			$count++;
		}

		$qs .= 'ELSE ' . $count . ' ';
		$qs .= 'END';

		// dd($bindings);

		return $query->orderByRaw($qs, $bindings);
	}

	public function scopeOrderByWithNulls($query, $column, $sort = 'asc', $nulls = 'first')
	{
		if (empty($column))
			throw new InvalidArgumentException('Column argument must be not null');

		$sort = (mb_strtolower($sort) == 'asc') ? 'asc' : 'desc';
		$nulls = (mb_strtolower($nulls) == 'first') ? 'first' : 'last';

		$array = explode('.', $column);

		$column = null;

		if (isset($array[0]))
			$column .= '"' . $array[0] . '"';
		if (isset($array[1]))
			$column .= '."' . $array[1] . '"';

		return $query->orderByRaw('' . $column . ' ' . $sort . ' nulls ' . $nulls . '');
	}

	/**
	 * Set the keys for a save update query.
	 *
	 * @param Builder $query
	 * @return Builder
	 */
	protected function setKeysForSaveQuery(Builder $query)
	{
		$keys = $this->getKeyName();
		if (!is_array($keys)) {
			return parent::setKeysForSaveQuery($query);
		}

		foreach ($keys as $keyName) {
			$query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
		}

		return $query;
	}

	/**
	 * Get the primary key value for a save query.
	 *
	 * @param mixed $keyName
	 * @return mixed
	 */
	protected function getKeyForSaveQuery($keyName = null)
	{
		if (is_null($keyName)) {
			$keyName = $this->getKeyName();
		}

		if (isset($this->original[$keyName])) {
			return $this->original[$keyName];
		}

		return $this->getAttribute($keyName);
	}
}