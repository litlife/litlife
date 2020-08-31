<?php

namespace App\Jobs;

use App\Book;
use App\BookGroup;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookCountInGroup
{
	use Dispatchable;

	protected $group;

	/**
	 * Create a new job instance.
	 *
	 * @param BookGroup $group
	 * @return void
	 */
	public function __construct(BookGroup $group)
	{
		$this->group = $group;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws
	 */
	public function handle()
	{
		// получаем количетво книг в группе

		$this->group->books_count = $this->group->books()->accepted()->count();

		if ($this->group->books_count <= 1) {
			// если не найдено ни одной книги в группе, то удаляем группу или если осталась одна книга в группе, то для этой книги можно убрать группу и вообще удалить группу
		} else {
			// проверяем, есть ли главная книга

			$main_book = Book::where('group_id', $this->group->id)
				->where('main_in_group', true)
				->first();

			// если не существует главной книги в группе, то ищем ее

			if (empty($main_book)) {
				$main_book = $this->group->bookWithBestRating();

				$main_book->main_in_group = true;
				$main_book->save();
			}
		}

		// получаем количетво книг в группе

		$this->group->books_count = $this->group->books()->accepted()->count();
		$this->group->save();
	}
}
