<?php

namespace App\Console\Commands\Old;

use App\Attachment;
use App\Book;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;

class OldBookToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:book {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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

		Book::any()->orderBy('id')->chunk($limit, function ($items) {
			foreach ($items as $item) {
				echo($item->id . "\n");
				$this->book($item);
			}
		});
	}

	public function book($book)
	{
		$formats = explode(',', $book->old_formats);

		$formats_new = [];

		foreach ($formats as $format) {
			$s = trim(strtolower($format));

			if ($s) {
				$formats_new[] = strtolower($format);
			}
		}

		if (count($formats_new) > 0) {
			$book->formats = $formats_new;
		} else {
			$book->formats = null;
		}

		if (preg_match('/\[(С|C)И\]/iu', $book->title)) {
			$book->title = trim(preg_replace('/\[(С|C)И\]/iu', '', $book->title));
			$book->is_si = true;
		}

		if (preg_match('/\((С|C)И\)/iu', $book->title)) {
			$book->title = trim(preg_replace('/\((С|C)И\)/iu', '', $book->title));
			$book->is_si = true;
		}

		if (preg_match('/\(ЛП\)/iu', $book->title)) {
			$book->title = trim(preg_replace('/\(ЛП\)/iu', '', $book->title));
			$book->is_lp = true;
		}

		if (preg_match('/\[ЛП\]/iu', $book->title)) {
			$book->title = trim(preg_replace('/\[ЛП\]/iu', '', $book->title));
			$book->is_lp = true;
		}

		// добавляем в attachments информацию со старых обложек книг

		$cover = $book->old_covers()
			->orderBy('type', 'desc')
			->first();

		if (!empty($cover)) {

			$filePath = $this->getOldBookPath($book->id) . '/' . $cover->name;

			echo("path " . $filePath . "\n");

			if (Storage::disk('old')->exists($filePath)) {
				$attachment = new Attachment;
				$attachment->storage = 'old';
				$attachment->book_id = $book->id;
				$attachment->name = $cover->name;
				$attachment->content_type = Storage::disk('old')->mimeType($filePath);
				$attachment->size = Storage::disk('old')->size($filePath);
				$attachment->type = 'image';
				$attachment->parameters = [
					'w' => $cover->width,
					'h' => $cover->height
				];

				if (empty($cover->time)) {
					$attachment->created_at = Storage::disk('old')->lastModified($filePath);
				} else {
					$attachment->created_at = Carbon::createFromTimestamp($cover->time)->toDateTimeString();
				}

				$attachment->dirname = $this->getOldBookPath($book->id);
				$attachment->save();

				$book->cover_id = $attachment->id;
			}
		}

		$book->save();
	}

	function getOldBookPath($BookId)
	{
		$FolderName1 = (floor($BookId / 1000000) * 1000000);
		$FolderName2 = (floor($BookId / 1000) * 1000);
		return 'Book/' . $FolderName1 . '/' . $FolderName2 . '/' . $BookId;
	}
}
