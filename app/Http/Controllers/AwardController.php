<?php

namespace App\Http\Controllers;

use App\Award;
use App\Http\Requests\StoreAward;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class AwardController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		$search = request()->search;

		$awards = Award::when($search, function ($query, $search) {
			return $query->searchPartWord($search);
		})->orderBy('title', 'asc')
			->paginate();

		if (request()->ajax())
			return $awards;
		else
			return view('award.index', compact('awards'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 * @throws
	 */

	public function create()
	{
		$this->authorize('create', Award::class);

		return view('award.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreAward $request
	 * @return Response
	 * @throws
	 */

	public function store(StoreAward $request)
	{
		$this->authorize('create', Award::class);

		$award = new Award;
		$award->fill($request->all());
		$award->save();

		return redirect()->route('awards.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Award $award
	 * @return View
	 */

	public function show(Award $award)
	{
		return view('award.show', compact('award'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Award $award
	 * @return View
	 * @throws
	 */

	public function edit(Award $award)
	{
		$this->authorize('update', $award);

		return view('award.edit', compact('award'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreAward $request
	 * @param Award $award
	 * @return Response
	 * @throws
	 */

	public function update(StoreAward $request, Award $award)
	{
		$this->authorize('update', $award);

		$award->fill($request->all());
		$award->save();

		return back();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 * @throws
	 */

	public function destroy($id)
	{
		$award = Award::withTrashed()->findOrFail($id);

		if ($award->trashed()) {
			$this->authorize('restore', $award);
			$award->restore();
		} else {
			$this->authorize('delete', $award);
			$award->delete();
		}

		return $award;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @return array
	 */

	public function search(Request $request)
	{
		$query = Award::with('image')->void();

		if (!empty($request->q))
			$query->similaritySearch($request->q);

		$awards = $query->simplePaginate();

		return $awards;
	}
}
