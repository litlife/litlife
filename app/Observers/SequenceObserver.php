<?php

namespace App\Observers;

use App\Events\UserCreatedSequencesCountChanged;
use App\Jobs\User\UpdateUserCreatedSequencesCount;
use App\Sequence;
use Illuminate\Support\Facades\Cache;

class SequenceObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Sequence $sequence
	 * @return void
	 */
	public function creating(Sequence $sequence)
	{
		$sequence->autoAssociateAuthUser();

		//$sequence->description = Purify::clean($sequence->description);
	}

	/**
	 * Listen to the User deleting event.
	 *
	 * @param Sequence $sequence
	 * @return void
	 */
	public function updating(Sequence $sequence)
	{
		// $sequence->description = Purify::clean($sequence->description);
	}

	public function created(Sequence $sequence)
	{
		$this->updateUserCreatedSequencesCount($sequence);

		Cache::forever('sequences_count_refresh', 'true');
	}

	/**
	 * Обновляем количество авторов у пользователя
	 *
	 * @param Sequence $sequence
	 * @return void
	 */

	public function updateUserCreatedSequencesCount(Sequence $sequence)
	{
		if (!empty($sequence->create_user)) {
			UpdateUserCreatedSequencesCount::dispatch($sequence->create_user);
		}
	}

	public function deleted(Sequence $sequence)
	{
		$this->updateUserCreatedSequencesCount($sequence);

		Cache::forever('sequences_count_refresh', 'true');
	}

	public function restored(Sequence $sequence)
	{
		$this->updateUserCreatedSequencesCount($sequence);

		Cache::forever('sequences_count_refresh', 'true');
	}
}