<?php

namespace App\Http\Controllers;

use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\Http\Requests\StoreSupportRequest;
use App\SupportRequest;
use App\SupportRequestMessage;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportRequestController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$this->authorize('view_unsolved', SupportRequest::class);

		$supportRequests = SupportRequest::latest()
			->with('create_user.avatar', 'latest_message.create_user.avatar')
			->simplePaginate();

		return view('support_request.index', ['supportRequests' => $supportRequests]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function unsolved()
	{
		$this->authorize('view_unsolved', SupportRequest::class);

		$supportRequests = SupportRequest::whereStatusIn(['OnReview'])
			->with('create_user.avatar', 'latest_message.create_user.avatar')
			->oldest()
			->simplePaginate();

		return view('support_request.index', ['supportRequests' => $supportRequests]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function inProcessOfSolving()
	{
		$supportRequests = SupportRequest::whereStatusIn(['ReviewStarts'])
			->with('create_user.avatar', 'latest_message.create_user.avatar')
			->oldest()
			->simplePaginate();

		return view('support_request.index', ['supportRequests' => $supportRequests]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function solved()
	{
		$this->authorize('view_answered', SupportRequest::class);

		$supportRequests = SupportRequest::latest()
			->with('create_user.avatar', 'latest_message.create_user.avatar')
			->accepted()
			->simplePaginate();

		return view('support_request.index', ['supportRequests' => $supportRequests]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(User $user)
	{
		$this->authorize('create_support_requests', $user);

		return view('support_request.create', ['user' => $user]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreSupportRequest $request, User $user)
	{
		$this->authorize('create_support_requests', $user);

		DB::beginTransaction();

		$supportRequest = new SupportRequest($request->all());

		$user->createdSupportRequests()->save($supportRequest);

		$message = new SupportRequestMessage($request->all());
		$message->create_user_id = $user->id;

		$supportRequest
			->messages()
			->save($message);

		if (empty($supportRequest->title)) {
			$supportRequest->title = Str::limit($message->text, 100);
			$supportRequest->save();
		}

		event(new NumberOfUnsolvedSupportRequestsHasChanged($user));

		DB::commit();

		return redirect()
			->route('support_requests.show', ['support_request' => $supportRequest->id])
			->with('success', __('Question to support has been sent successfully'));
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
	 * Mark the support request as resolved
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function solve(SupportRequest $supportRequest)
	{
		$this->authorize('solve', $supportRequest);

		$supportRequest->statusAccepted();
		$supportRequest->save();

		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->create_user));
		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->status_changed_user));

		SupportRequest::flushNumberInProcess();
		SupportRequest::flushNumberOfSolved();

		if (request()->ajax())
			return view('support_request.status', ['item' => $supportRequest]);
		else {
			if ($supportRequest->isAuthUserCreator())
				return redirect()
					->route('users.support_requests.index', ['user' => $supportRequest->create_user])
					->with('success', __('Thank you! You marked the support request as resolved'));
			else
				return redirect()
					->route('support_requests.unsolved')
					->with('success', __('Thank you! You marked the support request as resolved'));
		}
	}

	/**
	 * Start reviewing a support request
	 *
	 * @param SupportRequest $supportRequest
	 * @return Response
	 * @throws
	 */
	public function startReview(SupportRequest $supportRequest)
	{
		$this->authorize('startReview', $supportRequest);

		$supportRequest->statusReviewStarts();
		$supportRequest->save();

		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->create_user));
		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->status_changed_user));

		SupportRequest::flushNumberInProcess();
		SupportRequest::flushNumberOfSolved();

		if (request()->ajax())
			return view('support_request.status', ['item' => $supportRequest]);
		else
			return redirect()
				->route('support_requests.show', $supportRequest)
				->with(['success' => __('You have started reviewing the request')]);
	}

	/**
	 * Прекратить рассматривать жалобу
	 *
	 * @param SupportRequest $supportRequest
	 * @return Response
	 * @throws
	 */
	public function stopReview(SupportRequest $supportRequest)
	{
		$this->authorize('stopReview', $supportRequest);

		$supportRequest->statusSentForReview();
		$supportRequest->save();

		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->create_user));
		event(new NumberOfUnsolvedSupportRequestsHasChanged($supportRequest->status_changed_user));

		SupportRequest::flushNumberInProcess();
		SupportRequest::flushNumberOfSolved();

		if (request()->ajax())
			return view('support_request.status', ['item' => $supportRequest]);
		else
			return redirect()
				->route('support_requests.unsolved')
				->with(['success' => __('You refused to resolve the request')]);
	}
}
