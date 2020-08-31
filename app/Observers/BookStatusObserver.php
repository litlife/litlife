<?php

namespace App\Observers;

use App\Book;
use App\BookStatus;
use App\Enums\ReadStatus;
use App\Jobs\Book\UpdateBookReadStatusCount;
use App\Jobs\User\UpdateUserReadStatusCount;
use App\User;

class BookStatusObserver
{
	public function saving(BookStatus $status)
	{
		if (empty($status->origin_book_id))
			$status->origin_book_id = $status->book_id;
	}

	public function saved(BookStatus $status)
	{
		$original_status = (object)$status->getOriginal();

		if (!isset($original_status->status) or $status->status != $original_status->status) {
			$book = Book::any()->find($status->book_id);
			$user = User::any()->find($status->user_id);

			if (!empty($status->status)) {
				if (!empty($book))
					UpdateBookReadStatusCount::dispatch($book, $status->status);

				if (!empty($user))
					UpdateUserReadStatusCount::dispatch($user, $status->status);
			}

			if (!empty($original_status->status)) {

				if (!empty($book))
					UpdateBookReadStatusCount::dispatch($book, $original_status->status);

				if (!empty($user))
					UpdateUserReadStatusCount::dispatch($user, $original_status->status);
			}

			if (!empty($book))
				$book->save();

			if (!empty($user))
				$user->save();

			if ($status->status == ReadStatus::Readed) {
				$remembered_page = $book->remembered_pages()
					->where('user_id', $user->id);

				if (!empty($remembered_page))
					$remembered_page->delete();
			}

			//$this->deleteAllOtherBookStatusesFromSameGroup($status);
		}
	}

	public function deleteAllOtherBookStatusesFromSameGroup(BookStatus $status)
	{
		if (!empty($status->originBook)) {
			if ($status->originBook->isInGroup()) {
				$status->user->book_read_statuses()
					->whereNotIn('book_id', function ($query) use ($status) {
						if ($status->originBook->isMainInGroup()) {
							$query->select('id')
								->from('books')
								->where('main_book_id', $status->originBook->id);
						} elseif ($status->originBook->isNotMainInGroup()) {
							$query->select('id')
								->from('books')
								->where(function ($query) use ($status) {
									$query->where('main_book_id', $status->originBook->mainBook->id)
										->where('id', '!=', $status->originBook->id);
								})
								->orWhere('id', $status->originBook->mainBook->id);
						}
					})
					->delete();
			}
		}
	}
}