<?php

namespace App\Traits;

use App\User;

trait UserCreate
{
	public function autoAssociateAuthUser()
	{
		if (empty($this->create_user) and auth()->check())
			$this->create_user()->associate(auth()->user());
	}

	public function create_user()
	{
		return $this->belongsTo('App\User', $this->getCreateUserIdColumn(), 'id');
	}

	public function getCreateUserIdColumn()
	{
		return defined('static::CREATE_USER_ID') ? static::CREATE_USER_ID : 'create_user_id';
	}

	public function associateAuthUser()
	{
		$this->create_user()->associate(auth()->user());
	}

	public function isUserCreator(User $user)
	{
		if ($user->id == $this->{$this->getCreateUserIdColumn()})
			return true;
		else
			return false;
	}

	public function isHaveAccess()
	{
		if (($this->isPrivate()) and (!$this->isAuthUserCreator()))
			return false;
		else
			return true;
	}

	public function isAuthUserCreator()
	{
		if (auth()->id() == $this->{$this->getCreateUserIdColumn()})
			return true;
	}

	public function scopeWhereCreator($query, User $user)
	{
		return $query->where($this->getCreateUserIdColumn(), $user->id);
	}
}