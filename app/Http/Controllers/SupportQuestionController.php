<?php

namespace App\Http\Controllers;

use App\Events\NumberOfUnsolvedSupportQuestionsHasChanged;
use App\FeedbackSupportResponses;
use App\Http\Requests\StoreFeedbackSupportQuestion;
use App\Http\Requests\StoreSupportQuestion;
use App\Http\Requests\UpdateSupportQuestionCategory;
use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfNewQuestions;
use App\Jobs\User\UpdateUserNumberInProgressQuestions;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\Feature\SupportQuestion\SupportQuestionStoreTest;
use Illuminate\Database\Eloquent\Builder;

class SupportQuestionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function support()
	{
		$this->authorize('create_support_questions', auth()->user());

		$supportQuestions = auth()->user()
			->createdSupportQuestions()
			->first();

		if (empty($supportQuestions))
			return redirect()
				->route('support_questions.create', ['user' => auth()->user()]);
		else
			return redirect()
				->route('users.support_questions.index', ['user' => auth()->user()]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function index()
	{
		$this->authorize('view_index', SupportQuestion::class);

		$supportQuestions = SupportQuestion::latest()
			->with('create_user.avatar', 'latest_message.create_user.avatar')
			->simplePaginate();

		return view('support_question.index', ['supportQuestions' => $supportQuestions]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function unsolved()
	{
		$this->authorize('view_index', SupportQuestion::class);

		$supportQuestions = SupportQuestion::whereStatusIn(['OnReview'])
			->with('create_user.avatar', 'latest_message.create_user.avatar', 'status_changed_user')
			->oldest()
			->simplePaginate();

		return view('support_question.index', ['supportQuestions' => $supportQuestions]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function inProcessOfSolving()
	{
		$this->authorize('view_index', SupportQuestion::class);

		$supportQuestions = SupportQuestion::whereStatusIn(['ReviewStarts'])
			->with('create_user.avatar', 'latest_message.create_user.avatar', 'status_changed_user')
			->orderByDesc(
                SupportQuestionMessage::select('created_at')
                    ->whereColumn('support_question_id', 'support_questions.id')
                    ->whereColumn('support_questions.create_user_id', 'support_question_messages.create_user_id')
                    ->limit(1)
            )
			->simplePaginate();

		return view('support_question.index', ['supportQuestions' => $supportQuestions]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function solved()
	{
		$this->authorize('view_index', SupportQuestion::class);

		$supportQuestions = SupportQuestion::latest()
			->with('create_user.avatar', 'latest_message.create_user.avatar', 'feedback', 'status_changed_user')
			->accepted()
			->simplePaginate();

		return view('support_question.index', ['supportQuestions' => $supportQuestions]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function create(User $user)
	{
		$this->authorize('create_support_questions', $user);

		return view('support_question.create', ['user' => $user]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function store(StoreSupportQuestion $request, User $user)
	{
		$this->authorize('create_support_questions', $user);

		DB::beginTransaction();

		$supportQuestion = new SupportQuestion($request->all());

		$user->createdSupportQuestions()->save($supportQuestion);

		$message = new SupportQuestionMessage($request->all());
		$message->create_user_id = $user->id;

		$supportQuestion
			->messages()
			->save($message);

		if (empty($supportQuestion->title)) {
			$supportQuestion->title = $message->text;
			$supportQuestion->save();
		}

		UpdateNumberOfNewQuestions::dispatch();

		DB::commit();

		return redirect()
			->route('support_questions.show', ['support_question' => $supportQuestion->id])
			->with('success', __('Question to support has been sent successfully'));
	}

	/**
	 * Display the question
	 *
	 * @param SupportQuestion $supportQuestion
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function show(SupportQuestion $supportQuestion)
	{
		$this->authorize('show', $supportQuestion);

		$messages = $supportQuestion
			->messages()
			->with('create_user')
			->latest()
			->simplePaginate();

		return view('support_question.show', [
			'supportQuestion' => $supportQuestion,
			'messages' => $messages
		]);
	}

	/**
	 * Mark the support request as resolved
	 *
	 * @param SupportQuestion $supportQuestion
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function solve(SupportQuestion $supportQuestion)
	{
		$this->authorize('solve', $supportQuestion);

		$supportQuestion->statusAccepted();
		$supportQuestion->save();

		UpdateNumberOfAnsweredQuestions::dispatch();
		UpdateNumberInProgressQuestions::dispatch();

		if ($supportQuestion->status_changed_user) {
			UpdateUserNumberInProgressQuestions::dispatch($supportQuestion->status_changed_user);
		}

		if (request()->ajax())
			return view('support_question.status', ['item' => $supportQuestion]);
		else {
			if ($supportQuestion->isAuthUserCreator())
				return redirect()
					->route('support_questions.show', $supportQuestion)
					->with('success', __('Thank you! You marked the support question as resolved'));
			else
				return redirect()
					->route('support_questions.unsolved')
					->with('success', __('Thank you! You marked the support question as resolved'));
		}
	}

	/**
	 * Start reviewing a support question
	 *
	 * @param SupportQuestion $supportQuestion
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function startReview(SupportQuestion $supportQuestion)
	{
		$this->authorize('startReview', $supportQuestion);

		$supportQuestion->statusReviewStarts();
		$supportQuestion->save();

		UpdateNumberOfNewQuestions::dispatch();
		UpdateNumberInProgressQuestions::dispatch();

		if (request()->ajax())
			return view('support_question.status', ['item' => $supportQuestion]);
		else
			return redirect()
				->route('support_questions.show', $supportQuestion)
				->with(['success' => __('You have started reviewing the question')]);
	}

	/**
	 * Stop reviewing a support question
	 *
	 * @param SupportQuestion $supportQuestion
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function stopReview(SupportQuestion $supportQuestion)
	{
		$this->authorize('stopReview', $supportQuestion);

		if ($supportQuestion->status_changed_user) {
			UpdateUserNumberInProgressQuestions::dispatch($supportQuestion->status_changed_user);
		}

		$supportQuestion->statusSentForReview();
		$supportQuestion->save();
		$supportQuestion->refresh();

		UpdateNumberOfNewQuestions::dispatch();
		UpdateNumberInProgressQuestions::dispatch();

		if ($supportQuestion->status_changed_user) {
			UpdateUserNumberInProgressQuestions::dispatch($supportQuestion->status_changed_user);
		}

		if (request()->ajax())
			return view('support_question.status', ['item' => $supportQuestion]);
		else
			return redirect()
				->route('support_questions.unsolved')
				->with(['success' => __('You refused to resolve the question')]);
	}

	/**
	 * Create feedback support question
	 *
	 * @param SupportQuestion $supportQuestion
	 * @throws AuthorizationException
	 */
	public function feedbackCreate(SupportQuestion $supportQuestion)
	{
		$this->authorize('create_feedback', $supportQuestion);

		return view('support_question.feedback.create', ['supportQuestion' => $supportQuestion]);
	}

	/**
	 * Store feedback support question
	 *
	 * @param SupportQuestion $supportQuestion
	 * @return \Illuminate\Http\Response
	 * @throws AuthorizationException
	 */
	public function feedbackStore(StoreFeedbackSupportQuestion $request, SupportQuestion $supportQuestion)
	{
		$this->authorize('create_feedback', $supportQuestion);

		$feedback = new FeedbackSupportResponses($request->all());
		$supportQuestion->feedback()->save($feedback);

		return redirect()
			->route('support_questions.show', $supportQuestion)
			->with('success', __('Thank you for your feedback!'));
	}

    /**
     * Question editing page
     *
     * @return \Illuminate\Http\Response
     * @param SupportQuestion $supportQuestion
     * @throws AuthorizationException
     */
    public function edit(SupportQuestion $supportQuestion)
    {
        $this->authorize('update', $supportQuestion);

        return view('support_question.edit', ['supportQuestion' => $supportQuestion]);
    }

    /**
     * Saving an edited question
     *
     * @param \Illuminate\Http\Request $request
     * @param SupportQuestion $supportQuestion
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function update(UpdateSupportQuestionCategory $request, SupportQuestion $supportQuestion)
    {
        $this->authorize('update', $supportQuestion);

        DB::beginTransaction();

        $supportQuestion->fill($request->all());
        $supportQuestion->save();

        DB::commit();

        return redirect()
            ->route('support_questions.edit', $supportQuestion)
            ->with('success', __('The data was successfully stored'));
    }
}
