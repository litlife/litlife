<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
	function index()
	{
		/*
		if (App::environment() == 'local') {
			Debugbar::startMeasure('browser', 'browser');

			Debugbar::stopMeasure('browser');

			Debugbar::startMeasure('view', 'Time of execture View');
		}

		$authors = Author::limit(10)->get();

		$view = view('test', ['value' => $value ?? '', 'authors' => $authors ?? ''])->render();

		if (App::environment() == 'local') {
			Debugbar::stopMeasure('view');
		}

		return $view;
		*/
	}

	public function test2()
	{


		return view('test2');
	}

	public function test_post()
	{
		//return back();

		//dd(request()->all());

		return back()->with([
			'sisyphus_ok' => request()->sisyphus ?? null
		]);
	}
}
