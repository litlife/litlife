<?php

namespace App\Observers;

use App\AdminNote;

class AdminNoteObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param AdminNote $adminNote
	 * @return void
	 */
	public function creating(AdminNote $adminNote)
	{
		$adminNote->autoAssociateAuthUser();
	}

	public function updating(AdminNote $adminNote)
	{

	}

	public function created(AdminNote $adminNote)
	{
		$adminNote->admin_noteable()->any()->first()->updateAdminNotesCount();
	}

	public function deleted(AdminNote $adminNote)
	{
		$adminNote->admin_noteable()->any()->first()->updateAdminNotesCount();
	}

	public function restored(AdminNote $adminNote)
	{
		$adminNote->admin_noteable()->any()->first()->updateAdminNotesCount();
	}
}