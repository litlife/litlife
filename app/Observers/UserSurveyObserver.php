<?php

namespace App\Observers;

use App\UserSurvey;

class UserSurveyObserver
{
	/**
	 * Handle the survey "creating" event.
	 *
	 * @param \App\UserSurvey $survey
	 * @return void
	 */
	public function creating(UserSurvey $survey)
	{
		$survey->autoAssociateAuthUser();
	}

	/**
	 * Handle the survey "created" event.
	 *
	 * @param \App\UserSurvey $survey
	 * @return void
	 */
	public function created(UserSurvey $survey)
	{
		//
	}

	/**
	 * Handle the survey "updated" event.
	 *
	 * @param \App\UserSurvey $survey
	 * @return void
	 */
	public function updated(UserSurvey $survey)
	{
		//
	}

	/**
	 * Handle the survey "deleted" event.
	 *
	 * @param \App\UserSurvey $survey
	 * @return void
	 */
	public function deleted(UserSurvey $survey)
	{
		//
	}

	/**
	 * Handle the survey "restored" event.
	 *
	 * @param \App\UserSurvey $survey
	 * @return void
	 */
	public function restored(UserSurvey $survey)
	{
		//
	}

	/**
	 * Handle the survey "force deleted" event.
	 *
	 * @param \App\UserSurvey $survey
	 * @return void
	 */
	public function forceDeleted(UserSurvey $survey)
	{
		//
	}
}
