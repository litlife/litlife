<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Book;
use App\Jobs\AttachmentRenameJob;
use Illuminate\Console\Command;
use Litlife\Url\Url;

class BookAttachmentRenameExtensions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attachments:rename_extensions {book_id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет расширения к вложениям книг по типу файла';
	protected $book;
	protected $attachments;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->book = Book::any()->findOrFail($this->argument('book_id'));

		$this->attachments = $this->book->attachments;

		foreach ($this->attachments as $attachment) {
			$this->attachment($attachment);
		}
	}

	public function attachment(Attachment $attachment)
	{
		$imagickFormat = mb_strtolower($attachment->getImagick()->getImageFormat());
		$extension = (string)Url::fromString($attachment->name)->getExtension();

		if ($imagickFormat == $extension)
			return false;

		$newName = (string)Url::fromString($attachment->name)->withExtension($imagickFormat);

		if ($this->isNameExists($newName)) {
			$newName = Url::fromString($newName)->appendToFilename('_' . uniqid());
		}

		AttachmentRenameJob::dispatch($this->book, $attachment, $newName);
	}

	private function isNameExists($newName)
	{
		foreach ($this->attachments as $attachment) {
			if ($attachment->name == $newName) {
				return true;
			}
		}

		return false;
	}
}
