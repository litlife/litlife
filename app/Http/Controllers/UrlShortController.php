<?php

namespace App\Http\Controllers;

use App\UrlShort;
use Illuminate\Http\Response;

class UrlShortController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function redirect($key)
	{
		$urlShort = UrlShort::where('key', $key)->first();

		if (empty($urlShort))
			abort(404);
		else
			return redirect()
				->away($urlShort->getFullUrl());
	}
}
