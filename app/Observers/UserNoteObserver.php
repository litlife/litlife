<?php

namespace App\Observers;

use App\UserNote;
use Illuminate\Support\Facades\Artisan;

class UserNoteObserver
{
	public function creating(UserNote $note)
	{
		$note->autoAssociateAuthUser();
	}

	public function saved(UserNote $note)
	{
		if (empty($note->external_images_downloaded)) {
			Artisan::call('images:download_external', [
				'model' => 'user_note', 'id' => $note->id
			]);
		}
	}
}
