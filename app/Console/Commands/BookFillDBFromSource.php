<?php

namespace App\Console\Commands;

use App\Book;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Jobs\Notification\BookFinishParseJob;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Jobs\UpdateGenreBooksCount;
use App\Library\AddEpubFile;
use App\Library\AddFb2File;
use App\Scopes\CheckedScope;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Litlife\BookConverter\Facades\BookConverter;
use Throwable;

class BookFillDBFromSource extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:fill_db_from_source {book_id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Функция берет источник файла книги и конвертирует и добавляет в базу данных';

	protected $book;
	protected $source;
	protected $filePath;
	private $file;
	private $stream;
	private $extension;

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

		if ($this->book->parse->isProgress()) {
			if (!$this->confirm('The book is already being processed. Want to continue anyway?'))
				return false;
		}

		// устанавливаем статус что книга находится на обработке
		$this->book->parse->start();
		$this->book->push();

		DB::beginTransaction();

		try {
			$this->book->online_read_new_format = true;
			$this->book->save();

			$this->file = $this->book->source()->withoutGlobalScope(CheckedScope::class)->first();

			if (empty($this->file) or !$this->file->exists())
				throw new Exception('Source file not found');

			$this->info('Start to process the book: ' . $this->book->id . "\n");

			$this->stream = $this->file->getStreamOrFirstFileInArchiveStream();
			$this->extension = $this->file->extension;

			$this->convertToFb2();
			$this->addFromFile();
			$this->success();

			$this->book->changed();
			$this->book->save();

			DB::commit();

		} catch (Exception $e) {
			DB::rollback();
			$this->failed($e);

		} catch (Throwable $e) {
			DB::rollback();
			$this->failed($e);
		}
	}

	private function convertToFb2()
	{
		// конвертируем в fb2 файлы остальные форматы
		if (!in_array($this->extension, ['fb2', 'epub'])) {

			if (in_array($this->extension, ['doc'])) {

				$this->stream = BookConverter::with('abiword')
					->open($this->stream, $this->extension)
					->convertToFormat('docx')
					->getFileStream();

				$this->extension = 'docx';
			}

			$this->stream = BookConverter::with('calibre')
				->open($this->stream, $this->extension)
				->convertToFormat('fb2')
				->getFilePath();

			$this->extension = 'fb2';
		}
	}

	public function addFromFile()
	{
		$this->book->forceDeleteSectionNote();

		if ($this->book->parse->isParseOnlyPages()) {
			$this->book->forceDeleteImagesExceptCover();
		} else {
			$this->book->forceDeleteImages();
		}


		switch ($this->extension) {
			case 'epub':

				$add = new AddEpubFile($this->book);
				$add->open($this->stream);

				break;

			case 'fb2':

				$add = new AddFb2File($this->book);
				$add->open($this->stream);

				break;

			default:
				throw new Exception('Extension not supported');
		}

		$add->addImages();

		if (!$this->book->parse->isParseOnlyPages()) {
			$add->description();
		}

		$add->addSections();
		$add->addNotes();

		if ($this->book->parse->isParseOnlyPages()) {
			if (empty($this->book->cover)) {
				$add->addCover();
			}

			if (empty($this->book->annotation)) {
				$add->addAnnotation();
			}
		}

		$this->book->save();

		$this->book->refreshSectionsCount();
		$this->book->refreshNotesCount();
		$this->book->refreshAttachmentCount();
		$this->book->refreshPagesCount();
		$this->book->refreshBookAgeCount();

		foreach ($this->book->genres()->notMain()->get() as $genre) {
			UpdateGenreBooksCount::dispatch($genre);
		}

		foreach ($this->book->authors()->any()->get() as $author) {
			UpdateAuthorBooksCount::dispatch($author);
		}

		foreach ($this->book->sequences()->any()->get() as $sequence) {
			UpdateSequenceBooksCount::dispatch($sequence);
		}

		$this->book->updatePageNumbers();
	}

	/**
	 * The command success to process.
	 *
	 * @param void
	 * @return void
	 */
	private function success()
	{
		$this->book->parse->success();
		$this->book->push();

		BookFinishParseJob::dispatch($this->book->parse);
	}

	/**
	 * The command failed to process.
	 *
	 * @param Exception $exception
	 * @return void
	 * @throws Exception $exception
	 */
	private function failed($exception)
	{
		$this->book->parse->failed([
			'message' => $exception->getMessage(),
			'code' => $exception->getCode(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'traceAsString' => $exception->getTraceAsString()
		]);

		$this->book->push();

		throw $exception;
	}

	public function setBook($book)
	{
		$this->book = $book;
	}

	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	public function setStream($stream)
	{
		$this->stream = $stream;
	}
}
