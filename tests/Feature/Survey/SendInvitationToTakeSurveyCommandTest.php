<?php

namespace Tests\Feature\Survey;

use App\Notifications\SendingInvitationToTakeSurveyNotification;
use App\User;
use App\UserSurvey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendInvitationToTakeSurveyCommandTest extends TestCase
{
	public function testSend()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->data->save();

		$this->assertNull($user->data->invitation_to_take_survey_has_been_sent);

		$this->artisan('survey:send_invitations', [
			'onlyUsersWhoRegisteredLaterThanTheDate' => $user->created_at,
			'daysPassedSinceDateRegistration' => 0
		])
			->assertExitCode(0);

		$user->refresh();

		$this->assertTrue($user->data->invitation_to_take_survey_has_been_sent);

		Notification::assertSentTo($user, SendingInvitationToTakeSurveyNotification::class);
	}

	public function testNotitication()
	{
		Notification::fake();

		$user = factory(User::class)->create();

		$user->notify(new SendingInvitationToTakeSurveyNotification($user));

		Notification::assertSentTo($user,
			SendingInvitationToTakeSurveyNotification::class,
			function ($notification, $channels) use ($user) {
				$this->assertContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				$mail = $notification->toMail($user);

				$this->assertEquals(__('notification.greeting') . ', ' . $user->userName . '!', $mail->greeting);

				$this->assertEquals(__('notification.sending_invitation_to_take_survey.subject'), $mail->subject);

				$this->assertEquals(__('notification.sending_invitation_to_take_survey.line'), $mail->introLines[0]);

				$this->assertStringContainsString('/surveys/guest/create?user=' . $user->id . '&signature=', $mail->actionUrl);

				return $notification->user->id == $user->id;
			}
		);
	}

	public function testDontSendIfAlreadySent()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->data->invitation_to_take_survey_has_been_sent = true;
		$user->data->save();

		$this->assertTrue($user->data->invitation_to_take_survey_has_been_sent);

		$this->artisan('survey:send_invitations', [
			'onlyUsersWhoRegisteredLaterThanTheDate' => $user->created_at,
			'daysPassedSinceDateRegistration' => 0
		])
			->assertExitCode(0);

		$user->refresh();

		Notification::assertNotSentTo($user, SendingInvitationToTakeSurveyNotification::class);
	}

	public function testDontSendIfAfterDateArgumentIsNewerThanTime()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->data->invitation_to_take_survey_has_been_sent = false;
		$user->data->save();

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		$this->artisan('survey:send_invitations', [
			'onlyUsersWhoRegisteredLaterThanTheDate' => $user->created_at->addMinute(),
			'daysPassedSinceDateRegistration' => 0
		])
			->assertExitCode(0);

		$user->refresh();

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		Notification::assertNotSentTo($user, SendingInvitationToTakeSurveyNotification::class);
	}

	public function testDontSendIfSurveyAlreadyFilled()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->data->invitation_to_take_survey_has_been_sent = false;
		$user->data->save();

		$survey = new UserSurvey();
		$user->surveys()->save($survey);

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		$this->artisan('survey:send_invitations', [
			'onlyUsersWhoRegisteredLaterThanTheDate' => $user->created_at,
			'daysPassedSinceDateRegistration' => 0
		])
			->assertExitCode(0);

		$user->refresh();

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		Notification::assertNotSentTo($user, SendingInvitationToTakeSurveyNotification::class);
	}

	public function testDoNotSendItIfAWeekHasNotPassedSinceTheRegistrationDate()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->data->invitation_to_take_survey_has_been_sent = false;
		$user->data->save();

		Carbon::setTestNow(now()->addWeek()->subMinute());

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		$this->artisan('survey:send_invitations', [
			'onlyUsersWhoRegisteredLaterThanTheDate' => $user->created_at,
			'daysPassedSinceDateRegistration' => 7
		])
			->assertExitCode(0);

		$user->refresh();

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		Notification::assertNotSentTo($user, SendingInvitationToTakeSurveyNotification::class);
	}

	public function testSendIfAWeekHasAlreadyPassedSinceTheRegistrationDate()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->data->invitation_to_take_survey_has_been_sent = false;
		$user->data->save();

		Carbon::setTestNow(now()->addWeek()->addMinute());

		$this->assertFalse($user->data->invitation_to_take_survey_has_been_sent);

		$this->artisan('survey:send_invitations', [
			'onlyUsersWhoRegisteredLaterThanTheDate' => $user->created_at,
			'daysPassedSinceDateRegistration' => 7
		])
			->assertExitCode(0);

		$user->refresh();

		$this->assertTrue($user->data->invitation_to_take_survey_has_been_sent);

		Notification::assertSentTo($user, SendingInvitationToTakeSurveyNotification::class);
	}
}
