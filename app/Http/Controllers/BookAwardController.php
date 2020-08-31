<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\StoreBookAward;

class BookAwardController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function index(Book $book)
	{
		$book_awards = $book->awards()
			->simplePaginate();

		return view('book.award.index', compact('book', 'book_awards'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws
	 */

	public function store(StoreBookAward $request, Book $book)
	{
		$this->authorize('attachAward', $book);

		$award = $book->awards()->updateOrCreate(
			['award_id' => $request->award],
			['year' => $request->year]
		);

		return back();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Book $book , Award $award
	 * @return \Illuminate\Http\Response
	 * @throws
	 */

	public function destroy(Book $book, $award)
	{
		$award = $book->awards()->where('award_id', $award)->firstOrFail();

		$this->authorize('attachAward', $book);
		$award->delete();

		return $award;
	}


}
