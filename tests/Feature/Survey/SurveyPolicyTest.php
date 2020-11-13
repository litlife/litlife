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
        $user = User::factory()->create();
        $user->group->view_user_surveys = false;
        $user->push();

        $this->assertFalse($user->can('viewUserSurveys', User::class));
    }

    public function testCanIfHasPermissions()
    {
        $user = User::factory()->create();
        $user->group->view_user_surveys = true;
        $user->push();

        $this->assertTrue($user->can('viewUserSurveys', User::class));
    }

    public function testCanTakeSurveyIfAlreadyPass()
    {
        $survey = UserSurvey::factory()->create();

        $user = $survey->create_user;

        $this->assertFalse($user->can('takeSurvey', User::class));
    }

    public function testCanCompleteTheSurveyIfAWeekHasPassedSinceTheRegistrationDate()
    {
        $user = User::factory()->create();

        Carbon::setTestNow(now()->addWeek()->addMinute());

        $this->assertTrue($user->can('takeSurvey', User::class));
    }

    public function testYouCannotCompleteTheSurveyIfAWeekHasNotPassedSinceTheRegistrationDate()
    {
        $user = User::factory()->create();

        Carbon::setTestNow(now()->addWeek()->subHour());

        $this->assertFalse($user->can('takeSurvey', User::class));
    }
}
