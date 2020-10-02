<?php

namespace App\Http\Controllers;

use App\AdBlock;
use App\Http\Requests\StoreAdBlock;
use Illuminate\Support\Facades\DB;

class AdBlockController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$this->authorize('index', AdBlock::class);

		$blocks = AdBlock::latest()
			->simplePaginate();

		return view('ads.index', ['blocks' => $blocks]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$this->authorize('create', AdBlock::class);

		return view('ads.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreAdBlock $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreAdBlock $request)
	{
		$this->authorize('create', AdBlock::class);

		$adBlock = new AdBlock($request->all());
		$adBlock->user_updated_at = now();
		$adBlock->save();

		return redirect()
			->route('ad_blocks.index')
			->with(['success' => __('Ad block created successfully')]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\AdBlock $adBlock
	 * @return \Illuminate\Http\Response
	 */
	public function edit(AdBlock $adBlock)
	{
		$this->authorize('update', $adBlock);

		return view('ads.edit', ['block' => $adBlock]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreAdBlock $request
	 * @param \App\AdBlock $adBlock
	 * @return \Illuminate\Http\Response
	 */
	public function update(StoreAdBlock $request, AdBlock $adBlock)
	{
		$this->authorize('update', $adBlock);

		$adBlock->fill($request->all());
		$adBlock->user_updated_at = now();
		$adBlock->save();

		return redirect()
			->route('ad_blocks.index')
			->with(['success' => __('The data was successfully stored')]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\AdBlock $adBlock
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(AdBlock $adBlock)
	{
		$this->authorize('delete', $adBlock);

		$adBlock->delete();

		return redirect()
			->route('ad_blocks.index')
			->with(['success' => __('Ad block was successfully deleted')]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\AdBlock $adBlock
	 * @return \Illuminate\Http\Response
	 */
	public function enable(AdBlock $adBlock)
	{
		$this->authorize('enable', $adBlock);

		DB::transaction(function () use ($adBlock) {
			AdBlock::where('name', $adBlock->name)
				->update(['enabled' => false]);

			$adBlock->enable();
			$adBlock->save();
		});

		return redirect()
			->route('ad_blocks.index')
			->with(['success' => __('Ad block :name enabled', ['name' => $adBlock->name])]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\AdBlock $adBlock
	 * @return \Illuminate\Http\Response
	 */
	public function disable(AdBlock $adBlock)
	{
		$this->authorize('disable', $adBlock);

		DB::transaction(function () use ($adBlock) {
			$adBlock->disable();
			$adBlock->save();
		});

		return redirect()
			->route('ad_blocks.index')
			->with(['success' => __('Ad block :name disabled', ['name' => $adBlock->name])]);
	}
}
