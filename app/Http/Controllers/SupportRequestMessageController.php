<?php

namespace App\Http\Controllers;

use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\Http\Requests\StoreSupportRequestMessage;
use App\SupportRequest;
use App\SupportRequestMessage;

class SupportRequestMessageController extends Controller
{
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

		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->create_user));

		if ($supportRequest->status_changed_user)
			event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->status_changed_user));

		return redirect()
			->route('support_requests.show', ['support_request' => $supportRequest->id])
			->with('success', __('The message has been successfully sent'));
	}
}
