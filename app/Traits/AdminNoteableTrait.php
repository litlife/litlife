<?php

namespace App\Traits;

trait AdminNoteableTrait
{
	public function admin_note()
	{
		return $this->morphOne('App\AdminNote', 'admin_noteable');
	}

	public function latest_admin_notes()
	{
		return $this->morphMany('App\AdminNote', 'admin_noteable')
			->orderBy('created_at', 'desc')
			->with('create_user')
			->limit(2);
	}

	public function updateAdminNotesCount()
	{
		$this->admin_notes_count = $this->admin_notes()->count();
		$this->save();
	}

	public function admin_notes()
	{
		return $this->morphMany('App\AdminNote', 'admin_noteable');
	}
}