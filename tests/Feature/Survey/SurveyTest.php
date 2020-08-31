<?php

namespace Tests\Feature\Survey;

use App\User;
use App\UserSurvey;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SurveyTest extends TestCase
{
	public function testIndexRoute()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$this->actingAs($user)
			->get(route('surveys.index'))
			->assertOk();
	}

	public function testCreateRouteIsOk()
	{
		$user = factory(User::class)
			->create();

		Carbon::setTestNow(now()->addWeek()->addMinute());

		$this->actingAs($user)
			->get(route('surveys.create'))
			->assertOk();
	}

	public function testStoreRouteIsOk()
	{
		$user = factory(User::class)
			->create();

		Carbon::setTestNow(now()->addWeek()->addMinute());

		$str = $this->faker->realText(100);
		$str2 = $this->faker->realText(200);
		$what_file_formats_do_you_download = ['fb2', 'epub'];

		$this->actingAs($user)
			->post(route('surveys.store'), [
				'what_you_need_on_our_site' => $str,
				'what_do_you_like_on_the_site' => $str2,
				'what_file_formats_do_you_download' => $what_file_formats_do_you_download
			])
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('survey.your_responses_were_saved_successfully'));

		$survey = $user->surveys()->first();

		$this->assertNotNull($survey);

		$this->assertEquals($str, $survey->what_you_need_on_our_site);
		$this->assertEquals($str2, $survey->what_do_you_like_on_the_site);
		$this->assertEquals($what_file_formats_do_you_download, $survey->what_file_formats_do_you_download);
		$this->assertTrue($survey->isUserCreator($user));
	}

	public function testCreateGuest()
	{
		$user = factory(User::class)
			->create();

		$url = URL::signedRoute('surveys.guest.create', ['user' => $user->id]);

		$this->get($url)
			->assertOk()
			->assertSeeText(__('survey.do_you_read_books_or_download_them'));
	}

	public function testStoreGuest()
	{
		$user = factory(User::class)
			->create();

		$url = URL::signedRoute('surveys.guest.store', ['user' => $user->id]);

		$str = $this->faker->realText(100);
		$str2 = $this->faker->realText(200);
		$what_file_formats_do_you_download = ['fb2', 'epub'];

		$this->followingRedirects()
			->post($url, [
				'what_you_need_on_our_site' => $str,
				'what_do_you_like_on_the_site' => $str2,
				'what_file_formats_do_you_download' => $what_file_formats_do_you_download
			])
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('survey.your_responses_were_saved_successfully'));

		$survey = $user->surveys()->first();

		$this->assertNotNull($survey);

		$this->assertEquals($str, $survey->what_you_need_on_our_site);
		$this->assertEquals($str2, $survey->what_do_you_like_on_the_site);
		$this->assertEquals($what_file_formats_do_you_download, $survey->what_file_formats_do_you_download);
		$this->assertTrue($survey->isUserCreator($user));
	}

	public function testCreateGuestRedirectIfAuth()
	{
		$user = factory(User::class)
			->create();

		$url = URL::signedRoute('surveys.guest.create', ['user' => $user->id]);

		$this->actingAs($user)
			->get($url)
			->assertRedirect(route('surveys.create'));
	}

	public function testStoreGuestRedirectIfAuth()
	{
		$user = factory(User::class)
			->create();

		$url = URL::signedRoute('surveys.guest.store', ['user' => $user->id]);

		$this->actingAs($user)
			->post($url)
			->assertSessionHasNoErrors()
			->assertRedirect(route('surveys.store'));
	}

	public function testSurveyGuestCreateRouteSeeSurveySavedIfExists()
	{
		$survey = factory(UserSurvey::class)
			->create();

		$user = $survey->create_user;

		$url = URL::signedRoute('surveys.guest.create', ['user' => $user->id]);

		$this->get($url)
			->assertOk()
			->assertSeeText(__('survey.your_responses_were_saved_successfully'));

		$url = URL::signedRoute('surveys.guest.store', ['user' => $user->id]);

		$this->post($url)
			->assertOk()
			->assertSeeText(__('survey.your_responses_were_saved_successfully'));
	}

	public function testSurveyCreateShowSurveySaved()
	{
		$survey = factory(UserSurvey::class)
			->create();

		$user = $survey->create_user;

		$this->actingAs($user)
			->get(route('surveys.create'))
			->assertOk()
			->assertSeeText(__('survey.your_responses_were_saved_successfully'));

	}
}
