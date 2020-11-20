<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportQuestionMessage;
use App\Jobs\User\UpdateUserNumberInProgressQuestions;
use App\SupportQuestion;
use App\SupportQuestionMessage;

class SupportQuestionMessageController extends Controller
{
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreSupportQuestionMessage $request)
	{
		$supportQuestion = SupportQuestion::find($request->support_question);

		if (empty($supportQuestion)) {
			$supportQuestion = new SupportQuestion();
			$supportQuestion->save();
		} else {
			$this->authorize('createMessage', $supportQuestion);
		}

        $latestMessage = $supportQuestion->messages()
            ->latest()
            ->first();

		$message = new SupportQuestionMessage($request->all());

		if ($latestMessage instanceof SupportQuestionMessage)
        {
            if ($latestMessage->isAuthUserCreator() and $latestMessage->bb_text == $message->bb_text)
                return back()
                    ->withErrors(['bb_text' => __('You have already added a message with this text')]);
        }

		$supportQuestion
			->messages()
			->save($message);

		if ($supportQuestion->status_changed_user) {
			UpdateUserNumberInProgressQuestions::dispatch($supportQuestion->status_changed_user);
		}

		return redirect()
			->route('support_questions.show', ['support_question' => $supportQuestion->id])
			->with('success', __('The message has been successfully sent'));
	}
}
