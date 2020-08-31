<?php

namespace App\Observers;

use App\Author;
use App\Events\UserCreatedAuthorsCountChanged;
use App\Jobs\User\UpdateUserCreatedAuthorsCount;
use Cache;

class AuthorObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Author $author
	 * @return void
	 */
	public function creating(Author $author)
	{
		$author->autoAssociateAuthUser();
	}

	public function created(Author $author)
	{
		$this->updateUserCreatedAuthorsCount($author);
		$this->updateBooksHelper($author);

		Cache::forever('authors_count_refresh', 'true');

		$author->averageRatingForPeriod->save();
	}

	/**
	 * Обновляем количество авторов у пользователя
	 *
	 * @param Author $author
	 * @return void
	 */

	public function updateUserCreatedAuthorsCount(Author $author)
	{
		if (!empty($author->create_user))
			UpdateUserCreatedAuthorsCount::dispatch($author->create_user);
	}

	public function updateBooksHelper(Author $author)
	{
		$books = $author->any_books()
			->with('writers')
			->get();

		foreach ($books as $book) {
			$book->updateTitleAuthorsHelper();
			$book->save();
		}
	}

	public function updating(Author $author)
	{
		//$this->name_helper($author);
	}

	public function updated(Author $author)
	{
		/*
		if ($author->getOriginal('status') == StatusEnum::Private) {
			if (in_array($author->status, [StatusEnum::OnReview, StatusEnum::Accepted])) {
				$manager = $author->managers()
					->where('user_id', $author->create_user->id)
					->first();

				if (empty($manager))
					$manager = new Manager;

				$manager->create_user_id = '0';
				$manager->character = 'editor';
				$manager->user_id = $author->create_user->id;
				$manager->statusAccepted();
				$author->managers()->save($manager);
			}
		}
		*/
		if (
			$author->isChanged('last_name')
			or $author->isChanged('first_name')
			or $author->isChanged('middle_name')
			or $author->isChanged('nickname')
		)
			$this->updateBooksHelper($author);
	}

	/*
		public function name_helper(Author $author)
		{
			$author->name_helper = mb_substr($author->name, 0, 255);
		}
	*/

	public function deleted(Author $author)
	{
		$this->updateUserCreatedAuthorsCount($author);

		Cache::forever('authors_count_refresh', 'true');

		foreach ($author->managers as $manager) {
			$user = $manager->user;

			if (!empty($user)) {
				if (!$user->isAuthorGroupMustAttached())
					$user->detachAuthorGroup();
			}

			if ($manager->isPrivate())
				$manager->delete();
		}
	}

	public function restored(Author $author)
	{
		$this->updateUserCreatedAuthorsCount($author);

		Cache::forever('authors_count_refresh', 'true');

		$managers = $author->managers()
			->withTrashed()
			->with('user')
			->get();

		foreach ($managers as $manager) {
			$user = $manager->user;

			if (!$manager->trashed()) {
				if (!empty($user)) {
					if ($user->isAuthorGroupMustAttached())
						$user->attachAuthorGroup();
				}
			}

			if ($manager->isPrivate())
				$manager->restore();
		}
	}
}