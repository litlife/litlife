<?php

namespace App\Traits;

use App\Book;
use App\Jobs\Book\BookUngroupJob;

trait GroupTrait
{
	public function connect_user()
	{
		return $this->hasOne('App\User', 'id', 'connect_user_id');
	}

	public function scopeNotConnected($query)
	{
		return $query->whereNull($this->getTable() . '.main_book_id');
	}

	public function mainBook()
	{
		return $this->belongsTo('App\Book', 'main_book_id');
	}

	public function removeFromGroup()
	{
		BookUngroupJob::dispatch($this);
	}

	public function addToGroup($group, $main = false)
	{
		$this->group()->associate($group);
		$this->main_in_group = (boolean)$main;
		$this->connected_at = now();
		$this->connect_user_id = auth()->id();
	}

	public function group()
	{
		return $this->belongsTo('App\BookGroup', 'group_id', 'id');
	}

	// обновить количество книг у группе

	public function updateEditionsCount()
	{
		if (empty($this->main_book_id)) {
			$count = $this->groupedBooks()->count();

			$this->editions_count = $count;
			$this->save();

			$this->groupedBooks()
				->any()
				->update(['editions_count' => $this->editions_count]);

			return true;
		} else {
			if (!empty($mainBook = $this->mainBook)) {
				$mainBook->updateEditionsCount();
			}
		}
	}

	public function groupedBooks()
	{
		return $this->hasMany('App\Book', 'main_book_id');
	}

	public function setEditionsCountAttribute($value)
	{
		if (empty($value))
			$value = null;

		$this->attributes['editions_count'] = $value;
	}

	/**
	 * Принадлежит ли книга какой либо группе
	 *
	 * @return bool
	 */
	public function isInGroup()
	{
		if (!empty($this->main_book_id))
			return true;

		if (!empty($this->editions_count))
			return true;

		return false;
	}

	/**
	 * Не главная ли книга в группе
	 *
	 * @return bool
	 */
	public function isNotMainInGroup()
	{
		return !$this->isMainInGroup();
	}

	/**
	 * Главная ли книга в группе
	 *
	 * @return bool
	 */
	public function isMainInGroup()
	{
		if (empty($this->main_book_id) and $this->editions_count)
			return true;
		else
			return false;
	}

	public function isAttachedToBook(Book $book)
	{
		if (empty($this->mainBook))
			return false;

		if ($this->mainBook->is($book))
			return true;
		else
			return false;
	}
}