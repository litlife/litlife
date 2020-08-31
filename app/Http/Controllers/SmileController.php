<?php

namespace App\Http\Controllers;

use App\Smile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SmileController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$dt = Carbon::now();

		$query = Smile::void();

		if ((($dt->month == 12) and ($dt->day > 15)) or ($dt->month == 01)) {
			//$smiles = Smile::get();
		} else {
			$query->regular();
		}

		$smiles = $query->get();

		if (request()->ajax())
			return $smiles->map(function ($user) {
				return collect($user->toArray())
					->only(['name', 'simple_form', 'fullUrl'])
					->all();
			});
		else
			return view('smile.index', ['smiles' => $smiles]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}
