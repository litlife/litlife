<?php

namespace App\Http\Controllers;

use App\SupportRequest;
use Illuminate\Http\Request;

class SupportRequestController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$this->authorize('index', SupportRequest::class);

		$supportRequests = SupportRequest::latest()
			->simplePaginate();

		return view('support_request.index', ['supportRequests' => $supportRequests]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$this->authorize('create', SupportRequest::class);

		return view('support_request.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\SupportRequest $supportRequest
	 * @return \Illuminate\Http\Response
	 */
	public function show(SupportRequest $supportRequest)
	{
		$this->authorize('show', $supportRequest);

		$messages = $supportRequest
			->messages()
			->latest()
			->simplePaginate();

		return view('support_request.show', [
			'supportRequest' => $supportRequest,
			'messages' => $messages
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\SupportRequest $supportRequest
	 * @return \Illuminate\Http\Response
	 */
	public function edit(SupportRequest $supportRequest)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\SupportRequest $supportRequest
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, SupportRequest $supportRequest)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\SupportRequest $supportRequest
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(SupportRequest $supportRequest)
	{
		//
	}
}
