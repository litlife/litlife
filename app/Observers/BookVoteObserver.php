<?php

namespace App\Observers;

use App\Book;
use App\BookStatus;
use App\BookVote;
use App\Enums\ReadStatus;
use App\Jobs\User\UpdateUserBookVotesCount;
use App\User;

class BookVoteObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param BookVote $book_vote
	 * @return void
	 */
	public function creating(BookVote $book_vote)
	{
		$book_vote->autoAssociateAuthUser();

		if (empty($book_vote->origin_book_id))
			$book_vote->origin_book_id = $book_vote->book_id;
	}

	public function updating(BookVote $book_vote)
	{

	}

	/*
		public function deleting(Attachment $attachment)
		{
			Storage::delete($attachment->pathToFile);
		}
		*/
	public function created(BookVote $book_vote)
	{
		$this->setBookStatusReadedForUser($book_vote);
		$this->updateUserVoteCount($book_vote);
		$this->refreshBookRating($book_vote);
	}

	/**
	 * Устанавливаем статус книги прочитан
	 *
	 * @param  $book_vote
	 * @return void
	 */

	private function setBookStatusReadedForUser(&$book_vote)
	{
		$status = $book_vote->create_user
			->book_read_statuses
			->where('book_id', $book_vote->book_id)
			->first();

		if (empty($status) or (!empty($status) and
				!in_array($status->status, ['readed', 'read_not_complete', 'not_read']))) {

			if (empty($status)) {
				$status = new BookStatus();
				$status->user_id = $book_vote->create_user_id;
				$status->book_id = $book_vote->book_id;
			}

			$status->status = ReadStatus::Readed;
			$status->origin_book_id = $book_vote->book_id;
			$status->user_updated_at = now();
			$status->save();
		}
	}

	/**
	 * Обновим количество оценок пользователя
	 *
	 * @param  $book_vote
	 * @return void
	 */
	private function updateUserVoteCount(&$book_vote)
	{
	    if ($book_vote->create_user instanceof User)
        {
            UpdateUserBookVotesCount::dispatch($book_vote->create_user);
        }
	}

	/**
	 * Обновим оценку книги
	 *
	 * @param  $book_vote
	 * @return void
	 */
	private function refreshBookRating(&$book_vote)
	{
		if ($book_vote->book instanceof Book) {
			$book_vote->book->ratingChanged();
			$book_vote->book->save();
		}
	}

	public function updated(BookVote $book_vote)
	{
		//$this->deleteAllOtherVotesForBooksFromSameGroup($book_vote);
		$this->setBookStatusReadedForUser($book_vote);
		$this->refreshBookRating($book_vote);
	}

	public function deleted(BookVote $book_vote)
	{
		//$this->deleteAllOtherVotesForBooksFromSameGroup($book_vote);
		$this->updateUserVoteCount($book_vote);
		$this->refreshBookRating($book_vote);
	}

	public function restored(BookVote $book_vote)
	{
		//$this->deleteAllOtherVotesForBooksFromSameGroup($book_vote);
		$this->updateUserVoteCount($book_vote);
		$this->refreshBookRating($book_vote);
	}

	public function deleteAllOtherVotesForBooksFromSameGroup(BookVote $vote)
	{
		if (!empty($vote->originBook)) {
			if ($vote->originBook->isInGroup()) {
				$vote->user->votes()
					->whereNotIn('book_id', function ($query) use ($vote) {
						if ($vote->originBook->isMainInGroup()) {
							$query->select('id')
								->from('books')
								->where('main_book_id', $vote->originBook->id);
						} elseif ($vote->originBook->isNotMainInGroup()) {
							$query->select('id')
								->from('books')
								->where(function ($query) use ($vote) {
									$query->where('main_book_id', $vote->originBook->mainBook->id)
										->where('id', '!=', $vote->originBook->id);
								})
								->orWhere('id', $vote->originBook->mainBook->id);
						}
					})
					->delete();
			}
		}
	}
}