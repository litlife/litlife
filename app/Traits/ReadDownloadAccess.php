<?php

namespace App\Traits;

trait ReadDownloadAccess
{
	public function scopeOnlyDownloadAccess($query)
	{
		return $query->where($this->getTable() . ".download_access", true);
	}

	public function scopeOnlyReadAccess($query)
	{
		return $query->where($this->getTable() . ".read_access", true);
	}

	public function scopeReadAndDownloadAccess($query)
	{
		return $query->where($this->getTable() . ".download_access", true)
			->where($this->getTable() . ".read_access", true);
	}

	public function scopeReadOrDownloadAccess($query)
	{
		return $query->where($this->getTable() . ".download_access", true)
			->orWhere($this->getTable() . ".read_access", true);
	}

	public function secret_hide_user()
	{
		return $this->hasOne('App\User', 'id', 'secret_hide_user_id');
	}

	public function isReadOrDownloadAccess()
	{
		return $this->isReadAccess() || $this->isDownloadAccess();
	}

	public function isReadAccess()
	{
		return (bool)$this->read_access;
	}

	public function isDownloadAccess()
	{
		return (bool)$this->download_access;
	}

	public function readAccessEnable()
	{
		$this->read_access = true;
	}

	public function downloadAccessEnable()
	{
		$this->download_access = true;
	}

	public function readAccessDisable()
	{
		$this->read_access = false;
	}

	public function downloadAccessDisable()
	{
		$this->download_access = false;
	}
}