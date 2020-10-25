<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportRequestMessage;
use App\SupportRequest;
use App\SupportRequestMessage;
use Illuminate\Http\Request;

class SupportRequestMessageController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreSupportRequestMessage $request)
	{
		$supportRequest = SupportRequest::find($request->support_request);

		if (empty($supportRequest)) {
			$supportRequest = new SupportRequest();
			$supportRequest->save();
		} else {
			$this->authorize('createMessage', $supportRequest);
		}

		$message = new SupportRequestMessage($request->all());

		$supportRequest
			->messages()
			->save($message);

		return redirect()
			->route('support_requests.show', ['support_request' => $supportRequest->id])
			->with('success', __('The message has been successfully sent'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\SupportRequestMessage $supportRequestMessage
	 * @return \Illuminate\Http\Response
	 */
	public function show(SupportRequestMessage $supportRequestMessage)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\SupportRequestMessage $supportRequestMessage
	 * @return \Illuminate\Http\Response
	 */
	public function edit(SupportRequestMessage $supportRequestMessage)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\SupportRequestMessage $supportRequestMessage
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, SupportRequestMessage $supportRequestMessage)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\SupportRequestMessage $supportRequestMessage
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(SupportRequestMessage $supportRequestMessage)
	{
		//
	}
}
