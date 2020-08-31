<?php

namespace App\Observers;

use App\Attachment;
use App\Jobs\Book\UpdateBookAttachmentsCount;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Litlife\Url\Url;

class AttachmentObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Attachment $attachment
	 * @return void
	 */
	public function creating(Attachment $attachment)
	{
		$attachment->autoAssociateAuthUser();

		if (empty($attachment->size))
			$attachment->size = $attachment->imagick->getImageLength();

		if (empty($attachment->content_type))
			$attachment->content_type = $attachment->imagick->getImageMimeType();

		$attachment->name = Url::fromString($attachment->name)
			->withExtension(mb_strtolower($attachment->imagick->getImageFormat()));

		if ($attachment->book->attachments->count()) {
			$found_attachment = $attachment->book->attachments->first(function ($other_attachment) use ($attachment) {
				if ($other_attachment->name == $attachment->name)
					return $other_attachment;
			});

			if (!empty($found_attachment)) {
				$attachment->name = Url::fromString($attachment->name)->appendToFilename('_' . uniqid());
			}
		}
	}

	public function created(Attachment $attachment)
	{
		if (empty($attachment->dirname))
			$attachment->dirname = getPath($attachment->book_id) . '/' . $attachment->folder;

		$storage = Storage::disk($attachment->storage);

		if (is_resource($attachment->source)) {
			rewind($attachment->source);
			$storage->put($attachment->dirname . '/' . $attachment->name, $attachment->source);
		} elseif (file_exists($attachment->source)) {
			$storage->putFileAs($attachment->dirname, new File($attachment->source), $attachment->name);
		}

		$img = new Imagick();
		$img->readImageFile($attachment->getStream());
		$attachment->sha256_hash = $img->getImageSignature();

		$attachment->addParameter('w', $img->getImageWidth());
		$attachment->addParameter('h', $img->getImageHeight());

		// определяем mime type файла, если он не установлен вручную
		if (empty($attachment->content_type)) {
			$attachment->content_type = $storage->mimeType($attachment->dirname . '/' . $attachment->name);
		}

		if (empty($attachment->size))
			$attachment->size = $storage->size($attachment->dirname . '/' . $attachment->name);

		$attachment->save();
	}

	public function deleting(Attachment $attachment)
	{
		/*
		Storage::disk($attachment->storage)
			->delete($attachment->pathToFile);
		*/
	}

	public function deleted(Attachment $attachment)
	{
		if ($attachment->isForceDeleting()) {
			Storage::disk($attachment->storage)->delete($attachment->dirname . '/' . $attachment->name);
		}

		if (!empty($attachment->book))
			UpdateBookAttachmentsCount::dispatch($attachment->book);
	}
}