<?php

namespace App\Observers;

use App\Jobs\User\UpdateUserConfirmedMailboxCount;
use App\UserEmail;
use App\UserEmailToken;
use Illuminate\Support\Str;

class UserEmailObserver
{
	public function creating(UserEmail $email)
	{
		$email->isValidRefresh();
	}

	public function created(UserEmail $email)
	{
		$token = new UserEmailToken;
		$token->token = Str::random(32);

		$email->tokens()->save($token);

		if ($email->isConfirmed())
			$this->updateUserConfirmedMailboxCount($email->user);
	}

	public function updateUserConfirmedMailboxCount($user)
	{
		if (!empty($user)) {
			$user->refreshConfirmedMailboxCount();
			$user->save();
		}
	}

	public function updating(UserEmail $email)
	{

	}

	public function deleting(UserEmail $email)
	{

	}

	public function deleted(UserEmail $email)
	{
		if ($email->isConfirmed())
			$this->updateUserConfirmedMailboxCount($email->user);

		$email->tokens()->forceDelete();
	}

	public function restored(UserEmail $email)
	{
		if ($email->isConfirmed())
			$this->updateUserConfirmedMailboxCount($email->user);
	}
}