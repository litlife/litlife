<?php

namespace App\Observers;

use App\BookFile;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\UpdateBookFilesCount;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Litlife\Url\Url;

class BookFileObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param BookFile $file
	 * @return void
	 * @throws
	 */
	public function creating(BookFile $file)
	{
		$file->autoAssociateAuthUser();

		if (empty($file->dirname)) {
			// папка в хранилище
			$file->dirname = $file->generateDirName();
		}

		// создаем папку в которой будет лежать файл в хранилище
		Storage::disk($file->storage)
			->makeDirectory($file->dirname);

		$name = fileNameFormat($file->book->getNameForBookFile());

		if (empty($name))
			$name = 'file';

		$name = Str::finish($name, '_' . Str::random(5));

		$name = Url::fromString($name)
			->withExtension($file->format)
			->getBasename();

		$contents = stream_get_contents($file->stream, -1, 0);

		$file->md5 = md5($contents);
		$file->file_size = strlen($contents);

		// проверяем, входит ли файл в список которых не надо архивировать
		if ($file->isShouldBeArchived()) {

			// создаем архив во временной директории

			$archive_path = tmpfilePath();

			$archive = new Filesystem(new ZipArchiveAdapter($archive_path));

			$archive->write($name, $contents);

			$archive->getAdapter()->getArchive()->close();

			$file->name = Str::finish($name, '.zip');

			// отправляем файл в хранилище
			Storage::disk($file->storage)->putFileAs(
				$file->dirname,
				new File($archive_path),
				$file->name
			);
		} else {
			$file->name = $name;

			// отправляем файл в хранилище
			Storage::disk($file->storage)->put(
				$file->dirname . '/' . $file->name,
				$contents
			);
		}

		// определяем размер файла в хранилище
		$file->size = Storage::disk($file->storage)
			->size($file->dirname . '/' . $file->name);

		if (empty($file->size))
			$file->file_size = $file->size;
	}

	public function created(BookFile $file)
	{
		UpdateBookFilesCount::dispatch($file->book);
	}

	public function deleting(BookFile $book_file)
	{

	}

	public function deleted(BookFile $file)
	{
		UpdateBookFilesCount::dispatch($file->book);

		if ($file->isForceDeleting()) {
			$file->purgeDownloadLogs();

			Storage::disk($file->storage)
				->delete($file->dirname . '/' . $file->name);
		}

		if ($file->isSentForReview())
			BookFile::flushCachedOnModerationCount();

		if ($file->book->isFailedParse()) {
			if ($file->isSource())
				$file->book->parse->success();
		}
	}


	public function restored(BookFile $file)
	{
		UpdateBookFilesCount::dispatch($file->book);

		if ($file->isSentForReview())
			BookFile::flushCachedOnModerationCount();
	}

}