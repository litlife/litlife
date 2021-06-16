<?php

namespace App\Jobs\Book;

use App\Book;
use App\BookKeyword;
use App\Keyword;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookAddKeywordsJob
{
	use Dispatchable;

	protected $book;
	protected $keywords = [];
	protected $mainBook;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @param array $keywords
	 * @return void
	 */
	public function __construct(Book $book, array $keywords)
	{
		$this->book = $book;
		$this->keywords = $keywords;

        if ($this->book->isInGroup() and $this->book->isNotMainInGroup() and !empty($this->book->mainBook))
            $this->mainBook = $this->book->mainBook;
        else
            $this->mainBook = $book;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {
			$this->handleWithTransaction();
		});
	}

	public function handleWithTransaction()
	{
		foreach ($this->keywords as $keywordIdOrText) {
			if (is_numeric($keywordIdOrText)) {
				// введен id
				$keyword = Keyword::accepted()->find($keywordIdOrText);
			} elseif (!empty($keywordIdOrText)) {
				// ключевое слово текст и не пустое
				$keyword = Keyword::searchFullWord($keywordIdOrText)->accepted()->first();

				if (empty($keyword)) {

					// запрещаем добавление новых ключевых слов к приватным книгам
					if (!$this->mainBook->isAccepted())
						continue;

					$keyword = new Keyword;
					$keyword->text = $keywordIdOrText;
					if (auth()->user()->getPermission('book_keyword_add_new_with_check') or auth()->user()->getPermission('book_keyword_moderate')) {
						if ($this->mainBook->isPrivate())
							$keyword->statusPrivate();
						else {
							if (auth()->user()->getPermission('book_keyword_moderate'))
								$keyword->statusAccepted();
							elseif (auth()->user()->getPermission('book_keyword_add_new_with_check'))
								$keyword->statusSentForReview();
						}
						$keyword->save();
					} else {
						continue;
					}
				}
			}

			if (!empty($keyword)) {
				$book_keyword = $this->mainBook->book_keywords()
					->where('keyword_id', $keyword->id)
					->withTrashed()
					->first();

				if (empty($book_keyword)) {
					$book_keyword = new BookKeyword;
					$book_keyword->keyword_id = $keyword->id;
					$book_keyword->origin_book_id = $this->book->id;
					if ($this->mainBook->isPrivate())
						$book_keyword->statusPrivate();
					else {
						$book_keyword->status = $keyword->status;
					}
					$this->mainBook->book_keywords()->save($book_keyword);
				}

				if ($book_keyword->trashed()) {
					$book_keyword->restore();
				}
			}
		}
	}
}
