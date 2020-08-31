<?php

namespace App\Observers;

use App\AuthorPhoto;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class AuthorPhotoObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param AuthorPhoto $photo
	 * @return void
	 */
	public function creating(AuthorPhoto $photo)
	{
		$photo->name = $photo->author->name . ' ' . uniqid() . '.' . strtolower($photo->imagick->getImageFormat());

		$photo->height = $photo->imagick->getImageHeight();
		$photo->width = $photo->imagick->getImageWidth();
		$photo->type = '2';

		$photo->dirname = getPath($photo->author->id) . '/' . $photo->folder;

		if (is_resource($photo->source)) {
			rewind($photo->source);
			Storage::disk($photo->storage)
				->put($photo->dirname . '/' . $photo->name, $photo->source);
		} elseif (file_exists($photo->source)) {
			Storage::disk($photo->storage)
				->putFileAs($photo->dirname, new File($photo->source), $photo->name);
		} else {
			throw new Exception('resource or file not found');
		}

		if (empty($photo->size))
			$photo->size = $photo->imagick->getImageLength();

		$photo->autoAssociateAuthUser();
	}

	public function deleted(AuthorPhoto $photo)
	{
		if ($photo->isForceDeleting())
			Storage::disk($photo->storage)->delete($photo->dirname . '/' . $photo->name);
	}
}