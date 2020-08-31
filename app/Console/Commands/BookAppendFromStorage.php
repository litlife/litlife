<?php

namespace App\Console\Commands;

use App\Book;
use App\BookFile;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Litlife\Url\Url;

class BookAppendFromStorage extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:append_from_storage 
                                {--disk=public} 
                                {--directory=/} 
                                {--extensions=fb2} 
                                {--recursive=true} 
                                {--create_user_id=1}
                                {--remove_after_add=true}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Ищет файлы книг в указанной папке и добавляет в базу данных';
	protected $disk;
	protected $storage;
	protected $directory;
	protected $extensions = [];
	protected $recursive;
	protected $create_user_id;
	protected $remove_after_add;

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
		$this->defineOptions();

		if (!$this->storage->exists($this->directory)) {
			$this->error('Folder "' . $this->directory . '" not found in storage "' . $this->disk . '"');
			return false;
		}

		$this->directory($this->directory);

		if ($this->recursive) {
			$directories = $this->storage->allDirectories($this->directory);

			foreach ($directories as $directory) {
				$this->directory($directory);
			}
		}
	}

	private function defineOptions()
	{
		$this->disk = $this->option('disk');
		$this->directory = $this->option('directory');
		$this->extensions = explode(',', $this->option('extensions'));
		$this->recursive = $this->option('recursive');
		$this->create_user_id = $this->option('create_user_id');
		$this->remove_after_add = $this->option('remove_after_add');

		$this->storage = Storage::disk($this->disk);
	}

	public function directory($directory)
	{
		$this->info('Directory: ' . $directory);

		$files = $this->storage->allFiles($directory);

		foreach ($files as $path) {
			$this->file($path);
		}
	}

	public function file($path)
	{
		$path = Url::fromString($path);

		$this->info('File: ' . $path);

		if (!$this->isExtensionAllowed($path->getExtension()))
			return false;

		if ($this->isFileAlreadyExists($path)) {
			$this->warn('File ' . $path . ' already exists');
			return false;
		}

		if ($this->addBook($path)) {
			$this->removeFile($path);
		}
	}

	private function isExtensionAllowed($extension)
	{
		return in_array($extension, $this->extensions);
	}

	public function isFileAlreadyExists($path)
	{
		$stream = $this->storage
			->getDriver()
			->readStream($path);

		$md5 = md5(stream_get_contents($stream));

		$file = BookFile::where('md5', $md5)->exists();

		if ($file)
			return true;
		else
			return false;
	}

	protected function addBook($path)
	{
		return DB::transaction(function () use ($path) {

			$book = new Book;
			$book->title = $path->getBasename();
			$book->updateTitleAuthorsHelper();
			$book->create_user_id = $this->create_user_id;
			$book->save();

			$stream = $this->storage
				->getDriver()
				->readStream((string)$path);

			rewind($stream);

			$file = new BookFile;
			$file->open($stream, $path->getExtension());
			$file->create_user_id = $this->create_user_id;
			$file->source = true;
			$book->files()->save($file);

			fclose($stream);

			$book->parse->wait();
			$book->save();

			return true;
		});
	}

	protected function removeFile($path)
	{
		if ($this->remove_after_add) {
			if (!$this->storage->delete($path))
				throw new Exception('Error remove file ' . $path);
		}
	}
}