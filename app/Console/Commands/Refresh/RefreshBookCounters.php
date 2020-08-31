<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Enums\ReadStatus;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Jobs\Book\UpdateBookAge;
use App\Jobs\Book\UpdateBookAttachmentsCount;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookRating;
use App\Jobs\Book\UpdateBookReadStatusCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use Illuminate\Console\Command;

class RefreshBookCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:book_counters {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все счетчики книги';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$book = Book::any()->findOrFail($this->argument('id'));

		UpdateBookSectionsCount::dispatch($book);
		UpdateBookNotesCount::dispatch($book);
		UpdateBookAttachmentsCount::dispatch($book);
		UpdateBookAge::dispatch($book);
		UpdateBookFilesCount::dispatch($book);
		UpdateBookRating::dispatch($book);
		//UpdateBookPagesCount::dispatch($book);
		UpdateBookReadStatusCount::dispatch($book, ReadStatus::Readed);
		UpdateBookReadStatusCount::dispatch($book, ReadStatus::ReadLater);
		UpdateBookReadStatusCount::dispatch($book, ReadStatus::ReadNow);
		UpdateBookReadStatusCount::dispatch($book, ReadStatus::ReadNotComplete);
		UpdateBookReadStatusCount::dispatch($book, ReadStatus::NotRead);

		BookUpdatePageNumbersJob::dispatch($book);

		$book->refreshCharactersCount();
		$book->boughtTimesCountRefresh();
		$book->refreshFavoritesCount();
		$book->refreshGenresHelper();
		$book->refreshPrivateChaptersCount();
		$book->updateEditionsCount();
		$book->refreshCommentsCount();

		$book->save();
	}
}
