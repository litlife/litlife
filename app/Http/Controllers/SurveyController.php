<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvey;
use App\User;
use App\UserSurvey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SurveyController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->authorize('viewUserSurveys', User::class);

		$surveys = UserSurvey::latest()
			->simplePaginate();

		return view('survey.index', ['surveys' => $surveys]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (auth()->user()->surveys()->count())
			return view('success', ['success' => __('survey.your_responses_were_saved_successfully')]);

		$this->authorize('takeSurvey', User::class);

		return view('survey.form', ['action' => route('surveys.store')]);
	}

	public function store(StoreSurvey $request)
	{
		$this->authorize('takeSurvey', User::class);

		$survey = new UserSurvey();
		$survey->fill($request->all());
		$survey->save();

		return view('success', ['success' => __('survey.your_responses_were_saved_successfully')]);
	}

	public function createGuest(Request $request)
	{
		if (auth()->check())
			return redirect()->route('surveys.create');

		$user = User::findOrFail($request->user);

		if ($user->surveys()->count())
			return view('success', ['success' => __('survey.your_responses_were_saved_successfully')]);

		return view('survey.form', ['action' => URL::signedRoute('surveys.guest.store', ['user' => $user->id])]);
	}

	public function storeGuest(StoreSurvey $request)
	{
		if (auth()->check())
			return redirect()
				->route('surveys.store')->withInput();

		$user = User::findOrFail($request->user);

		if ($user->surveys()->count())
			return view('success', ['success' => __('survey.your_responses_were_saved_successfully')]);

		$survey = new UserSurvey();
		$survey->fill($request->all());
		$user->surveys()->save($survey);

		return view('success', ['success' => __('survey.your_responses_were_saved_successfully')]);
	}
}
