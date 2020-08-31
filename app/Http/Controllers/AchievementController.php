<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\Http\Requests\StoreAchievement;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AchievementController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		$achievements = Achievement::with('image')
			->simplePaginate();

		return view('achievement.index', compact('achievements'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 * @throws
	 */

	public function create()
	{
		$this->authorize('create', Achievement::class);

		return view('achievement.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreAchievement $request
	 * @return Response
	 * @throws
	 */

	public function store(StoreAchievement $request)
	{
		$this->authorize('create', Achievement::class);

		$this->validate($request,
			['image' => 'required|image|max:' . config('litlife.max_image_size') . ''],
			__('achievement'));

		$image = new Image;
		$image->openImage($request->image->getRealPath());
		$image->storage = config('filesystems.default');
		$image->size = $request->image->getSize();
		$image->name = $request->image->getClientOriginalName();
		$image->save();

		$achievement = new Achievement;
		$achievement->fill($request->all());
		$achievement->image()->associate($image);
		$achievement->save();

		return redirect()->route('achievements.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Achievement $achievement
	 * @return View
	 */

	public function show(Achievement $achievement)
	{
		return view('achievement.show', compact('achievement'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Achievement $achievement
	 * @return View
	 * @throws
	 */

	public function edit(Achievement $achievement)
	{
		$this->authorize('update', $achievement);

		return view('achievement.edit', compact('achievement'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreAchievement $request
	 * @param Achievement $achievement
	 * @return Response
	 * @throws
	 */

	public function update(StoreAchievement $request, Achievement $achievement)
	{
		$this->authorize('update', $achievement);

		$this->validate($request,
			['image' => 'image|max:' . config('litlife.max_image_size') . ''],
			__('achievement'));

		$achievement->fill($request->all());

		if (!empty($request->image)) {
			if (!empty($achievement->image)) {
				$achievement->image->delete();
			}

			$image = new Image;
			$image->openImage($request->image->getRealPath());
			$image->storage = config('filesystems.default');
			$image->size = $request->image->getSize();
			$image->name = $request->image->getClientOriginalName();
			$image->save();

			$achievement->image()->associate($image);
		}
		$achievement->save();

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
		$achievement = Achievement::withTrashed()->findOrFail($id);

		if ($achievement->trashed()) {
			$this->authorize('restore', $achievement);
			$achievement->restore();
		} else {
			$this->authorize('delete', $achievement);
			$achievement->delete();
		}

		return $achievement;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @return array
	 */

	public function search(Request $request)
	{
		$query = Achievement::with('image')->void();

		if (!empty($request->q))
			$query->similaritySearch($request->q);

		$achievements = $query
			->latest()
			->simplePaginate();

		foreach ($achievements as $achievement) {
			$achievement->image->max_width = 20;
			$achievement->image->max_height = 20;
			$achievement->image->quality = 95;
		}

		return $achievements;
	}
}
