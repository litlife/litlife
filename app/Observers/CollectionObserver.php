<?php

namespace App\Observers;

use App\Collection;
use App\User;

class CollectionObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Collection $collection
	 * @return void
	 */
	public function creating(Collection $collection)
	{
		$collection->autoAssociateAuthUser();
	}

	public function created(Collection $collection)
	{
		$this->updateUserCreatedCollectionsCount($collection->create_user);

		$collection->refreshUsersCount();
		$collection->save();
	}

	public function updateUserCreatedCollectionsCount(User $user)
	{
		$user->refreshCreatedCollectionsCount();
		$user->push();
	}

	public function updated(Collection $collection)
	{

	}

	public function deleted(Collection $collection)
	{
		$this->updateUserCreatedCollectionsCount($collection->create_user);
	}

	public function restored(Collection $collection)
	{
		$this->updateUserCreatedCollectionsCount($collection->create_user);
	}
}