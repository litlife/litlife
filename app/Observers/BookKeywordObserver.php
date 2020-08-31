<?php

namespace App\Observers;

use App\BookKeyword;

class BookKeywordObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param BookKeyword $book_keyword
	 * @return void
	 */
	public function creating(BookKeyword $book_keyword)
	{
		$book_keyword->autoAssociateAuthUser();

		if (empty($book_keyword->origin_book_id))
			$book_keyword->origin_book_id = $book_keyword->book_id;
	}

	public function restoring(BookKeyword $book_keyword)
	{

	}

	public function created(BookKeyword $book_keyword)
	{
		$this->updateAcceptedKeywordUseCount($book_keyword);
		$this->acceptKeyword($book_keyword);
	}

	/**
	 * Обновляем количество использований этого ключевого слова
	 *
	 */
	public function updateAcceptedKeywordUseCount(BookKeyword $book_keyword)
	{
		if (!empty($book_keyword->keyword)) {
			$book_keyword->keyword->updateBooksCount();
			$book_keyword->keyword->save();
		}
	}

	public function acceptKeyword(BookKeyword $book_keyword)
	{

		if ($book_keyword->isStatusChanged() and $book_keyword->isAccepted() and $book_keyword->keyword->isSentForReview()) {
			$book_keyword->keyword->statusAccepted();
			$book_keyword->keyword->save();
		}
	}

	public function updated(BookKeyword $book_keyword)
	{
		$this->acceptKeyword($book_keyword);
	}

	public function restored(BookKeyword $book_keyword)
	{
		$this->updateAcceptedKeywordUseCount($book_keyword);
	}

	public function deleted(BookKeyword $book_keyword)
	{
		$this->updateAcceptedKeywordUseCount($book_keyword);
		$this->deleteKeywordIfNotAcceptedAndZeroUseCount($book_keyword);
	}

	public function deleteKeywordIfNotAcceptedAndZeroUseCount(BookKeyword $book_keyword)
	{
		if (!empty($book_keyword->keyword)) {
			if ($book_keyword->keyword->isPrivate() or $book_keyword->keyword->isSentForReview()) {
				if ($book_keyword->keyword->count < 1) {
					$book_keyword->keyword->forceDelete();
				}
			}
		}
	}
}