<?php

namespace App\Traits;

use App\Model;

trait NestedItems
{
	public function scopeDescendants($query, $ids)
	{
		$ids = (array)$ids;
		foreach ($ids as $k => $id) {
			$ids[$k] = intval(preg_quote($id));
		}
		return $query->whereRaw('"tree" ~* ?', [',(' . implode('|', $ids) . '),']);
	}

	public function scopeOrDescendants($query, $ids)
	{
		$ids = (array)$ids;
		foreach ($ids as $k => $id) {
			$ids[$k] = intval(preg_quote($id));
		}
		return $query->orWhereRaw('"tree" ~* ?', [',(' . implode('|', $ids) . '),']);
	}

	public function scopeChilds($query, $ids)
	{
		$ids = (array)$ids;
		foreach ($ids as $k => $id) {
			$ids[$k] = intval(preg_quote($id));
		}
		return $query->whereRaw('"tree" ~* ?', [',(' . implode('|', $ids) . '),$']);
	}

	public function scopeRoots($query)
	{
		return $query->whereRaw('"tree" IS NULL ');
	}

	public function getRootAttribute()
	{
		$id = $this->getRootId();

		if (!empty($id))
			return self::any()->find($id);
		else
			return null;
	}

	public function getParentAttribute()
	{
		$id = $this->getParentId();

		if (!empty($id))
			return self::find($id);
		else
			return null;
	}

	public function setParentAttribute($parent)
	{
		if (!is_object($parent))
			$parent = self::find($parent);

		if (!is_integer($parent->id))
			throw new \LogicException('Parent must be defined');

		$array = $parent->getTree();
		array_push($array, $parent->id);
		$this->tree = $array;
	}

	public function getTree(): array
	{
		$array = explode(',', $this->tree);
		$array = array_filter($array);
		$array = array_values($array ?? []);
		return $array;
	}

	public function getLevelAttribute()
	{
		return $this->getLevel();
	}

	public function getLevelWithLimitAttribute()
	{
		$max = 3;

		if ($this->level < $max)
			return $this->level;
		else
			return $max;
	}

	public function isHaveDescendant(&$descendants)
	{
		if (isset($descendants)) {
			foreach ($descendants as $descendant) {
				if (preg_match('/\,' . $this->attributes['id'] . '\,$/i', $descendant->tree)) {
					return true;
				}
			}
		}

		return false;
	}

	public function isRoot(): bool
	{
		if ($this->getLevel() < 1)
			return true;
		else
			return false;
	}

	public function updateChildrenCount()
	{
		$this->children_count = self::childs($this->id)->count();
		$this->save();
	}

	public function updateLevel()
	{
		$array = explode(',', $this->tree);
		$array = array_unique($array);
		$array = array_filter($array);
		$array = array_values($array ?? []);
		$this->level = count($array);
	}

	public function getLevel()
	{
		return $this->attributes['level'];
	}

	public function isDescendantOf(Model $item): bool
	{
		return in_array($item->id, $this->getTree());
	}

	public function setTreeAttribute($value)
	{
		if (is_string($value))
			$value = explode(',', $value);

		$array = (array)$value;
		$array = array_unique($array);
		$array = array_filter($array);
		$array = array_values($array ?? []);

		if (count($array) > 0) {
			$this->attributes['tree'] = ',' . implode(',', $array) . ',';
			$this->attributes['level'] = count($array);
		} else {
			$this->attributes['tree'] = null;
			$this->attributes['level'] = 0;
		}
	}

	public function getParentId()
	{
		$array = $this->getTree();

		$array = array_reverse($array);

		return $array[0] ?? null;
	}

	public function getRootId()
	{
		$array = $this->getTree();

		return $array[0] ?? null;
	}
}