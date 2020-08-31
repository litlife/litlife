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
					if (!$this->book->isAccepted())
						continue;

					$keyword = new Keyword;
					$keyword->text = $keywordIdOrText;
					if (auth()->user()->getPermission('book_keyword_add_new_with_check') or auth()->user()->getPermission('book_keyword_moderate')) {
						if ($this->book->isPrivate())
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
				$book_keyword = $this->book->book_keywords()
					->where('keyword_id', $keyword->id)
					->withTrashed()
					->first();

				if (empty($book_keyword)) {
					$book_keyword = new BookKeyword;
					$book_keyword->keyword_id = $keyword->id;
					if ($this->book->isPrivate())
						$book_keyword->statusPrivate();
					else {
						$book_keyword->status = $keyword->status;
					}
					$this->book->book_keywords()->save($book_keyword);
				}

				if ($book_keyword->trashed()) {
					$book_keyword->restore();
				}
			}
		}
	}
}
