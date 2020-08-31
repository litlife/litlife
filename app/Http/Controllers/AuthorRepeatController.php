<?php

namespace App\Http\Controllers;

use App\Author;
use App\AuthorRepeat;
use App\Http\Requests\StoreAuthorRepeat;
use Illuminate\Http\Response;
use Illuminate\View\View;
use function request;

class AuthorRepeatController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return View
	 */
	public function index()
	{
		$author_repeats = AuthorRepeat::orderBy('created_at', 'desc')->with('authors', 'create_user')
			->simplePaginate();

		return view('author.repeat.index', compact('author_repeats'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 * @throws
	 */

	public function create()
	{
		$this->authorize('create', AuthorRepeat::class);

		$ids = request()->ids;
		if (!empty($ids) or $ids = old('authors'))
			$authors = Author::accepted()->find(explode(',', $ids));

		return view('author.repeat.create', ['authors' => $authors ?? []]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreAuthorRepeat $request
	 * @return Response
	 * @throws
	 */

	public function store(StoreAuthorRepeat $request)
	{
		$this->authorize('create', AuthorRepeat::class);

		$authorRepeat = new AuthorRepeat;
		$authorRepeat->comment = $request->comment;
		$authorRepeat->save();

		$array = Author::any()->whereIn('id', $request->authors)->pluck('id')->toArray();

		$authorRepeat->authors()->attach($array);

		return redirect()->route('author_repeats.index');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param AuthorRepeat $authorRepeat
	 * @return View
	 * @throws
	 */

	public function edit(AuthorRepeat $authorRepeat)
	{
		$this->authorize('update', $authorRepeat);

		return view('author.repeat.edit', compact('authorRepeat'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreAuthorRepeat $request
	 * @param AuthorRepeat $authorRepeat
	 * @return Response
	 * @throws
	 */

	public function update(StoreAuthorRepeat $request, AuthorRepeat $authorRepeat)
	{
		$this->authorize('update', $authorRepeat);

		$authorRepeat->comment = $request->comment;
		$authorRepeat->save();

		$array = Author::any()->whereIn('id', $request->authors)->pluck('id')->toArray();

		$authorRepeat->authors()->sync($array);

		return redirect()->route('author_repeats.index');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param AuthorRepeat $authorRepeat
	 * @return void
	 * @throws
	 */

	public function destroy(AuthorRepeat $authorRepeat)
	{
		$this->authorize('delete', $authorRepeat);

		$authorRepeat->delete();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param AuthorRepeat $authorRepeat
	 * @return Response
	 * @throws
	 */

	public function delete(AuthorRepeat $authorRepeat)
	{
		$this->authorize('delete', $authorRepeat);

		$authorRepeat->delete();

		return back();
	}
}
