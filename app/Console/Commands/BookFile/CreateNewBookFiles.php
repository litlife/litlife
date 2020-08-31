<?php

namespace App\Console\Commands\BookFile;

use App\Book;
use App\BookFile;
use App\Enums\StatusEnum;
use App\Library\CreateEpubFile;
use App\Scopes\CheckedScope;
use Illuminate\Console\Command;
use Illuminate\Queue\SerializesModels;
use Litlife\BookConverter\Facades\BookConverter;

class CreateNewBookFiles extends Command
{
	use SerializesModels;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bookfiles:make {bookId} ';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Функция содает новые файлы книг для определенной книги';

	private $book;
	private $txt;
	private $epub;

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
		$book_id = $this->argument('bookId');

		$this->book = Book::anyNotTrashed()
			->findOrFail($book_id);

		if ($this->book->isPagesNewFormat()) {
			if ($this->book->characters_count > 0 and $this->book->sections_count > 0) {
				$this->createEpubFile();
			}
		}

		$this->book->needCreateNewBookFilesDisable();
		$this->book->save();
	}


	public function createEpubFile()
	{
		$this->info('Начинаем создавать epub файл для книги ' . $this->book->title . ' ' . $this->book->id . ' ');

		$this->epub = new BookFile;
		$this->epub->format = 'epub';
		$this->epub->changeStatus(StatusEnum::getKey($this->book->status));

		if ($this->book->isSentForReview())
			$this->epub->statusAccepted();

		$this->epub->create_user_id = $this->book->create_user_id;

		$createEpubFile = new CreateEpubFile;
		$createEpubFile->setBookid($this->book->id);
		$createEpubFile->init();

		$stream = tmpfile();
		fwrite($stream, $createEpubFile->getEpub()->outputAsString());
		$path = stream_get_meta_data($stream)['uri'];

		$this->epub->stream = $stream;
		$this->epub->md5 = md5_file($path);
		$this->epub->file_size = filesize($path);
		$this->epub->auto_created = true;
		$this->book->files()->save($this->epub);

		BookFile::where('book_id', $this->book->id)
			->withoutGlobalScope(CheckedScope::class)
			->where('format', 'epub')
			->where('id', '!=', $this->epub->id)
			->delete();
	}

	public function createTxtFile()
	{
		// TODO нужно получить путь от файла

		$this->info('Начинаем создавать txt файл для книги ' . $this->book->title . ' ' . $this->book->id . ' ');

		$filePath = BookConverter::with('calibre')
			->open($this->epub->realFilePath)
			->convertToFormat('txt')
			->getFilePath();

		$this->txt = new BookFile;
		$this->txt->statusAccepted();
		$this->txt->zip = true;
		$this->txt->stream = fopen($filePath, 'w+');
		$this->txt->md5 = md5_file($filePath);
		$this->txt->file_size = filesize($filePath);
		$this->txt->format = 'txt';
		$this->txt->auto_created = true;
		$this->book->files()->save($this->txt);

		BookFile::where('book_id', $this->book->id)
			->where('format', 'txt')
			->where('id', '!=', $this->txt->id)
			->delete();
	}
}
