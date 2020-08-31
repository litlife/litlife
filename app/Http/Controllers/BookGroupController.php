<?php

namespace App\Http\Controllers;

use App\Book;
use App\Jobs\Book\BookGroupJob;
use App\Jobs\Book\BookMakeMainInGroupJob;
use App\Jobs\Book\BookUngroupJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BookGroupController extends Controller
{
	/**
	 * Список книг в группе
	 *
	 * @param Book $book
	 * @return View
	 * @throws
	 */
	public function editionsEdit(Request $request, Book $book)
	{
		$this->authorize('view_group_books', Book::class);

		if ($book->isInGroup() and $book->isNotMainInGroup())
			return redirect()
				->route('books.editions.edit', ['book' => $book->mainBook]);

		$books = $book->groupedBooks()->get();

		if ($books->count() > 0)
			$books->prepend($book);

		return view('book.group.books', ['main_book' => $book, 'books' => $books ?? null]);
	}

	/**
	 * Группирование книги с другой
	 *
	 * @param Request $request , Book $book
	 * @return Response
	 * @throws
	 */
	public function group(Request $request, Book $book)
	{
		$this->validate($request, ['edition_id' => 'numeric|exists:books,id'], [], __('book'));

		$this->authorize('group', $book);

		$edition = Book::accepted()->findOrFail($request->input('edition_id'));

		$this->authorize('group', $edition);

		if ($book->isInGroup() and $book->isNotMainInGroup())
			return back()
				->withErrors(['edition_id' => __('book_group.the_book_that_is_attached_to_must_be_the_main_one')]);

		if ($book->id == $edition->id)
			return back()
				->withErrors([__('book_group.book_to_be_attached_must_not_coincide_with_the_one_to_which_it_is_attached')]);

		if (!empty($edition->mainBook)) {
			if ($book->is($edition->mainBook)) {
				return back()
					->withErrors([__('book_group.book_is_already_attached_to_this_book')]);
			}
		}

		BookGroupJob::dispatch($book, $edition, true, true, false);

		return back()
			->with(['success' => __('book_group.grouped')]);
	}

	/**
	 * Разгруппирование книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */
	public function remove(Book $book)
	{
		$this->authorize('ungroup', $book);

		$mainBook = $book->mainBook;

		if (empty($book->main_book_id))
			return redirect()
				->route('books.editions.edit', $book)
				->withErrors([__('book_group.book_is_not_grouped')]);

		BookUngroupJob::dispatch($book, false);

		return redirect()
			->route('books.editions.edit', $mainBook)
			->with('success', __('book_group.ungrouped'));
	}

	/**
	 * Разгруппирование книги
	 *
	 * @param Book $book
	 * @return Response
	 * @throws
	 */

	public function makeMainInGroup(Book $book)
	{
		$this->authorize('make_main_in_group', $book);

		BookMakeMainInGroupJob::dispatch($book);

		return redirect()
			->route('books.editions.edit', $book)
			->with('success', __('book_group.maked_main_in_group'));
	}
}
