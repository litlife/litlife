<?php

namespace App\Observers;

use App\Book;
use App\BookFile;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Jobs\Book\BookUngroupJob;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Jobs\UpdateGenreBooksCount;
use App\Jobs\User\UpdateUserCreatedBooksCount;
use Cache;

class BookObserver
{
	/**
	 *
	 * @param Book $book
	 * @return void
	 */

	public function creating(Book $book)
	{
		$book->autoAssociateAuthUser();

		//$this->siAndLpHandle($book);
	}

	public function updating(Book $book)
	{
		if ($book->isChanged('title') or $book->wasRecentlyCreated)
			$book->updateTitleAuthorsHelper();
	}

	public function updated(Book $book)
	{

	}

	public function created(Book $book)
	{
		if ($book->isAccepted())
			$this->updateAuthorSequenceGenreBooksCount($book);

		$this->updateUserCreatedBooksCount($book);

		Cache::forever('books_count_refresh', 'true');

		$book->average_rating_for_period->save();
	}

	/**
	 * Обновляем у авторов, серии и жанров количество книг
	 *
	 * @param Book $book
	 * @return void
	 */

	public function updateAuthorSequenceGenreBooksCount(Book $book)
	{
		$book->genres->each(function ($genre) {
			UpdateGenreBooksCount::dispatch($genre);
		});

		$book->authors->each(function ($author) {
			UpdateAuthorBooksCount::dispatch($author);
			$author->flushUsersAddedToFavoritesNewBooksCount();
		});

		$book->sequences->each(function ($sequence) {
			UpdateSequenceBooksCount::dispatch($sequence);
		});
	}

	/**
	 * Обновляем количество книг у пользователя
	 *
	 * @param Book $book
	 * @return void
	 */

	public function updateUserCreatedBooksCount(Book $book)
	{
		if (!empty($book->create_user))
			UpdateUserCreatedBooksCount::dispatch($book->create_user);
	}

	public function deleted(Book $book)
	{
		if ($book->isAccepted())
			$this->updateAuthorSequenceGenreBooksCount($book);

		if ($book->isInGroup() and $book->isNotMainInGroup()) {
			$mainBook = $book->mainBook()->any()->first();

			if (!empty($mainBook)) {
				$mainBook->updateEditionsCount();
			}
		}

		$this->updateUserCreatedBooksCount($book);

		Cache::forever('books_count_refresh', 'true');

		if ($book->isForceDeleting()) {
			$this->forceDeleteRelations($book);
		}

		if ($book->isSentForReview()) {
			Book::flushCachedOnModerationCount();
			BookFile::flushCachedOnModerationCount();
		}
	}

	/**
	 * Перенесенно в модель Проверяем заголовок книги и заменяем, если есть метки СИ ЛП
	 *
	 * @param Book $book
	 * @return void
	 */
	/*
		public function siAndLpHandle(Book $book)
		{
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

			if (preg_match('/\[сборник\]/iu', $book->title)) {
				$book->title = trim(preg_replace('/\[сборник\]/iu', '', $book->title));
				$book->is_collection = true;
			}

			if (preg_match('/\(сборник\)/iu', $book->title)) {
				$book->title = trim(preg_replace('/\(сборник\)/iu', '', $book->title));
				$book->is_collection = true;
			}

			$book->title = trim($book->title);

			if (preg_match('/\.$/iu', $book->title) and !preg_match('/\.\.\.$/iu', $book->title) and !preg_match('/\.\.$/iu', $book->title)) {
				$book->title = trim(preg_replace('/(\.+)$/iu', '', $book->title));
			}
		}
	*/

	public function forceDeleteRelations(Book $book)
	{
		foreach ($book->attachments()->withTrashed()->get() as $attachment)
			$attachment->forceDelete();

		foreach ($book->sections()->any()->get() as $section)
			$section->forceDelete();

		foreach ($book->awards()->get() as $award)
			$award->forceDelete();

		foreach ($book->book_keywords()->withTrashed()->get() as $keyword)
			$keyword->forceDelete();

		foreach ($book->files()->any()->get() as $file)
			$file->forceDelete();

		$book->average_rating_for_period()->delete();
	}

	public function restored(Book $book)
	{
		if ($book->isAccepted())
			$this->updateAuthorSequenceGenreBooksCount($book);

		if ($book->isInGroup() and $book->isNotMainInGroup()) {
			$mainBook = $book->mainBook()->any()->first();

			if (empty($mainBook) or $mainBook->trashed()) {
				BookUngroupJob::dispatch($book, false);
			} else {
				$book->mainBook->updateEditionsCount();
			}
		}

		$this->updateUserCreatedBooksCount($book);

		Cache::forever('books_count_refresh', 'true');

		if ($book->isSentForReview()) {
			Book::flushCachedOnModerationCount();
			BookFile::flushCachedOnModerationCount();
		}
	}

}