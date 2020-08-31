<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Book;
use Illuminate\Console\Command;

class BookSearchAndAttachmentRenameExtensions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attachments:search_rename_extensions {last_book_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет расширения к вложениям книг по типу файла для тех книг где расширение не совпадает с шаблоном';

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
		$last_book_id = $this->argument('last_book_id');

		$attachments = Attachment::where('name', '!~', '(.*)(jpeg|jpg|png|gif)')
			->where('book_id', '>=', $last_book_id)
			->get();

		$book_ids = $attachments->pluck('book_id')->unique()->toArray();

		if (is_array($book_ids)) {
			Book::any()
				->whereIn('id', $book_ids)
				->where('id', '>=', $last_book_id)
				->chunkById(10, function ($items) {
					foreach ($items as $item) {
						$this->info('Book id ' . $item->id);

						$this->call('attachments:rename_extensions', ['book_id' => $item->id]);
					}
				});
		}
	}
}
