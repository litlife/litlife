<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKeyword;
use App\Keyword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

class KeywordController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$keywords = Keyword::orderBy('text', 'asc')
			->paginate();

		return view('keyword.index', ['keywords' => $keywords]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 * @throws
	 */
	public function create()
	{
		$this->authorize('create', Keyword::class);

		return view('keyword.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreKeyword $request
	 * @return Response
	 * @throws
	 */
	public function store(StoreKeyword $request)
	{
		$this->authorize('create', Keyword::class);

		if (Keyword::searchFullWord($request->text)->count() > 0)
			return redirect()
				->route('keywords.create')
				->withErrors([__('keyword.already_exists', ['text' => $request->text])]);

		$keyword = new Keyword($request->all());
		$keyword->save();

		return redirect()
			->route('keywords.index')
			->with(['success' => __('keyword.created', ['text' => $request->text])]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Keyword $keyword
	 * @return Response
	 * @throws
	 */
	public function edit(Keyword $keyword)
	{
		$this->authorize('update', $keyword);

		return view('keyword.edit', ['keyword' => $keyword]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreKeyword $request
	 * @param Keyword $keyword
	 * @return Redirector|RedirectResponse
	 * @throws
	 */
	public function update(StoreKeyword $request, Keyword $keyword)
	{
		$this->authorize('update', $keyword);

		$keyword->fill($request->all());
		$keyword->save();

		return redirect()->route('keywords.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return mixed
	 * @throws
	 */
	public function destroy($id)
	{
		$keyword = Keyword::withTrashed()->findOrFail($id);

		if ($keyword->trashed()) {
			$this->authorize('restore', $keyword);
			$keyword->restore();
		} else {
			$this->authorize('delete', $keyword);
			$keyword->delete();
		}

		if (request()->ajax())
			return $keyword;
		else
			return redirect()
				->route('keywords.index');
	}
}
