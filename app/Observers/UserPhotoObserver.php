<?php

namespace App\Observers;

use App\UserPhoto;
use Illuminate\Support\Facades\Storage;

class UserPhotoObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param UserPhoto $photo
	 * @return void
	 */
	public function creating(UserPhoto $photo)
	{
		$photo->autoAssociateAuthUser();

		if (empty($photo->dirname))
			$photo->dirname = getPath($photo->user->id) . '/' . $photo->folder;

		if ($photo->imagick->getImageWidth() > config('litlife.max_user_photo_width') or $photo->imagick->getImageHeight() > config('litlife.max_user_photo_height'))
			$photo->imagick->adaptiveResizeImage(config('litlife.max_user_photo_width'), config('litlife.max_user_photo_height'), true);

		$photo->name = $photo->user->userName . ' ' . time() . '.' . strtolower($photo->imagick->getImageFormat());

		$photo->parameters = ['w' => $photo->imagick->getImageWidth(), 'h' => $photo->imagick->getImageHeight()];

		Storage::disk($photo->storage)
			->put($photo->dirname . '/' . $photo->name, $photo->imagick->getImageBlob());

		$photo->size = $photo->getSize();
	}

	public function created(UserPhoto $photo)
	{
		$this->refreshUserPhotosCount($photo);
	}

	public function refreshUserPhotosCount($photo)
	{
		$photo->user->refreshPhotosCount();
		$photo->user->save();
	}

	public function deleting(UserPhoto $photo)
	{

	}

	public function deleted(UserPhoto $photo)
	{
		$this->refreshUserPhotosCount($photo);

		if ($photo->isForceDeleting()) {
			Storage::disk($photo->storage)->delete($photo->dirname . '/' . $photo->name);
		}
	}

	public function restored(UserPhoto $photo)
	{
		$this->refreshUserPhotosCount($photo);
	}
}