<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookTextProcessing;
use App\Http\Requests\StoreBookTextProcessing;
use Illuminate\Http\Response;

class BookTextProcessingController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @param Book $book
	 * @return Response
	 */
	public function index(Book $book)
	{
		$this->authorize('viewTextProcessing', $book);

		$textProcessings = $book->textProcessings()
			->with('create_user')
			->simplePaginate();

		return view('book.text_processing.index', compact('book', 'textProcessings'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Book $book
	 * @return Response
	 */
	public function create(Book $book)
	{
		$this->authorize('createTextProcessing', $book);

		return view('book.text_processing.create', compact('book'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Book $book
	 * @param StoreBookTextProcessing $request
	 * @return Response
	 */
	public function store(StoreBookTextProcessing $request, Book $book)
	{
		$this->authorize('createTextProcessing', $book);

		$processing = new BookTextProcessing();
		$processing->fill($request->all());
		$processing->autoAssociateAuthUser();
		$book->textProcessings()->save($processing);

		$book->forbid_to_change = true;
		$book->save();

		return redirect()
			->route('books.show', $book)
			->with(['success' => __('book_text_processing.processing_a_text_is_successfully_created')]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param BookTextProcessing $bookTextProcessing
	 * @return Response
	 */
	public function destroy(BookTextProcessing $bookTextProcessing)
	{
		//
	}
}
