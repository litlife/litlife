<?php

namespace Tests\Feature\Survey;

use App\User;
use App\UserSurvey;
use Carbon\Carbon;
use Tests\TestCase;

class SurveyPolicyTest extends TestCase
{
	public function testCantIfNotPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->view_user_surveys = false;
		$user->push();

		$this->assertFalse($user->can('viewUserSurveys', User::class));
	}

	public function testCanIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->view_user_surveys = true;
		$user->push();

		$this->assertTrue($user->can('viewUserSurveys', User::class));
	}

	public function testCanTakeSurveyIfAlreadyPass()
	{
		$survey = factory(UserSurvey::class)->create();

		$user = $survey->create_user;

		$this->assertFalse($user->can('takeSurvey', User::class));
	}

	public function testCanCompleteTheSurveyIfAWeekHasPassedSinceTheRegistrationDate()
	{
		$user = factory(User::class)->create();

		Carbon::setTestNow(now()->addWeek()->addMinute());

		$this->assertTrue($user->can('takeSurvey', User::class));
	}

	public function testYouCannotCompleteTheSurveyIfAWeekHasNotPassedSinceTheRegistrationDate()
	{
		$user = factory(User::class)->create();

		Carbon::setTestNow(now()->addWeek()->subHour());

		$this->assertFalse($user->can('takeSurvey', User::class));
	}
}
