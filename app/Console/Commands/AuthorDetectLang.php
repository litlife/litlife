<?php

namespace App\Console\Commands;

use App\Author;
use Illuminate\Console\Command;

class AuthorDetectLang extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'author:detect_lang {limit=10}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Функция ищет авторов у которых не проставлен язык и пытается определить его по книгам';

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
		$limit = $this->argument('limit');

		$authors = Author::where('books_count', '>', '0')
			->whereNull('lang')
			->orderByDesc('id')
			->limit($limit)
			->get();

		foreach ($authors as $author) {

			$this->author($author);
		}
	}

	public function author($author)
	{
		$author->updateLang();
		$author->save();

		$this->info('Для автора ' . $author->fullName . ' ' . $author->id . ' установлен язык "' . $author->lang . '"');
	}
}
