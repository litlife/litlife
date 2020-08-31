<?php

namespace App\Traits;

trait FavoritableTrait
{
	public function getPivotTable()
	{
		return static::FAVORITABLE_PIVOT_TABLE;
	}

	public function getAddedToFavoritesUsersCountColumnName()
	{
		return defined('static::ADDED_TO_FAVORITES_USERS_COUNT_COLUMN_NAME') ? static::ADDED_TO_FAVORITES_USERS_COUNT_COLUMN_NAME : 'added_to_favorites_count';
	}

	public function addedToFavoritesUsersCountRefresh()
	{
		$this->{$this->getAddedToFavoritesUsersCountColumnName()} = intval($this->addedToFavoritesUsers()->count());
		$this->save();
	}

	public function addedToFavoritesUsers()
	{
		return $this->belongsToMany('App\User', $this->getPivotTable());
	}
}