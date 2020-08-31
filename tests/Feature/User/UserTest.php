<?php

namespace Tests\Feature\User;

use App\Blog;
use App\Book;
use App\Jobs\CreateSiteAccountIfNotExists;
use App\Like;
use App\Notifications\NewLikeNotification;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserTest extends TestCase
{
	public function testIndexHttp()
	{
		$user = factory(User::class)->create()->fresh();

		$this->actingAs($user)
			->get(route('users'))
			->assertOk();
	}

	public function testEditProfileRightDate()
	{
		$user = factory(User::class)->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					'born_day' => '12',
					'born_month' => '02',
					'born_year' => '1993'
				]
			);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals('1993-02-12', $user->born_date->format('Y-m-d'));
	}

	public function testEditProfileWrongDate()
	{
		$user = factory(User::class)->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					'born_day' => '31',
					'born_month' => '02',
					'born_year' => '1993'
				]
			);
		//if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasErrorsIn('user', ['born_date' => __('validation.date', ['attribute' => __('user.born_date')])])
			->assertRedirect();
	}

	public function testEditProfileEmptyDate()
	{
		$user = factory(User::class)->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					//'born_day' => '31',
					//'born_month' => '02',
					//'born_year' => '1993'
				]
			);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testEditProfileDayEmpty()
	{
		$user = factory(User::class)->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					//'born_day' => '31',
					'born_month' => '02',
					'born_year' => '1993'
				]
			);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasErrorsIn('user', [
			'born_day' => __('validation.required_with', [
				'attribute' => __('user.born_day'), 'values' => __('user.born_month') . ' / ' . __('user.born_year')
			])
		])->assertRedirect();
	}

	public function testEditProfileIsOkIfEditEmpty()
	{
		$user = factory(User::class)->create(['born_date' => null])->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(route('users.edit', ['user' => $user->id]))
			->assertOk();
	}

	public function testFulltextSearch()
	{
		$author = User::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testDripOnline()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$now = now();

		$this->assertTrue($user->fresh()->isOnline());

		for ($a = 0; $a < 5; $a++) {
			$now = $now->addSeconds(config('genealabs-laravel-caffeine.drip-interval') / 1000)
				->addSecond();

			Carbon::setTestNow($now);

			$this->get(route('drip'))
				->assertStatus(204);

			$this->assertTrue($user->fresh()->isOnline());
		}

		$now = $now->addMinutes(config('litlife.user_last_activity'))
			->subSeconds(10);

		Carbon::setTestNow($now);

		$this->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$now = $now->addSeconds(config('genealabs-laravel-caffeine.drip-interval') / 1000);

		Carbon::setTestNow($now);

		$this->get(route('drip'))
			->assertStatus(204);

		$this->assertTrue($user->fresh()->isOnline());
	}

	public function testOfflineAfterActivityExpired()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$now = now();

		$this->assertTrue($user->fresh()->isOnline());

		$now = $now->addMinutes(config('litlife.user_last_activity'))
			->addSeconds(5);

		Carbon::setTestNow($now);

		$this->assertFalse($user->fresh()->isOnline());
	}

	// use InteractsWithSession;

	public function testSetCookiePassAgeRestriction()
	{
		$age = 12;

		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create(['age' => 18]);

		$this->actingAs($user)
			->get(route('user_pass_age_restriction', ['age' => $age]))
			->assertOk()
			->assertJson(['pass_age' => $age])
			->assertCookie('pass_age', $age)
			->assertCookieNotExpired('pass_age');
	}

	public function testEditProfileUserWithSameNickExists()
	{
		$nick = $this->faker->userName;

		$nick_uppercase = mb_strtoupper($nick);
		$nick_lowercase = mb_strtolower($nick);

		$user = factory(User::class)
			->create([
				'nick' => $nick_lowercase
			]);
		$user->group->edit_profile = true;
		$user->push();

		$user_with_same_nick = factory(User::class)
			->create([
				'nick' => $nick_uppercase
			]);

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => mb_ucfirst($nick_lowercase),
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'name_show_type' => 'Nick'
				]
			)
			->assertSessionHasErrors(['nick' => __('validation.user_nick_unique')], null, 'user')
			->assertRedirect();
	}

	public function testEditProfileUserChangeNickCaseNoErrors()
	{
		$nick = uniqid();

		$nick_uppercase = mb_strtoupper($nick);
		$nick_lowercase = mb_strtolower($nick);

		$user = factory(User::class)
			->create([
				'nick' => $nick_uppercase
			]);
		$user->group->edit_profile = true;
		$user->push();

		$this->assertEquals($user->nick, $nick_uppercase);

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => $nick_lowercase,
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'name_show_type' => 'Nick'
				]
			);
		if (!empty(session('errors'))) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals($user->nick, $nick_lowercase);
	}

	public function testSeeAds()
	{
		$user = factory(User::class)
			->create();
		$user->group->not_show_ad = false;
		$user->push();

		$this->assertTrue($user->can('see_ads', User::class));

		$user->group->not_show_ad = true;
		$user->push();

		$this->assertFalse($user->can('see_ads', User::class));

		$this->assertTrue((new User())->can('see_ads', User::class));
	}

	public function testDontShowAdsIfCreatedBooksMoreThan()
	{
		$user = factory(User::class)->create();
		$user->group->not_show_ad = false;
		$user->data->created_books_count = 9;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('see_ads', User::class));

		$user->data->created_books_count = 10;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('see_ads', User::class));

		$user->data->created_books_count = 11;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('see_ads', User::class));
	}

	public function testDontShowAdsIfPurchaseABook()
	{
		$user = factory(User::class)->create();
		$user->group->not_show_ad = false;
		$user->data->books_purchased_count = 0;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('see_ads', User::class));

		$user->data->books_purchased_count = 1;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('see_ads', User::class));
	}

	public function testUseShopPolicy()
	{
		$user = factory(User::class)
			->create();

		$this->assertTrue($user->group->shop_enable);

		$this->assertTrue($user->can('use_shop', User::class));

		$user->group->shop_enable = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('use_shop', User::class));
	}

	public function testRefreshCounters()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.refresh_counters', ['user' => $user]))
			->assertRedirect(route('profile', ['user' => $user]));
	}

	public function testCreateSiteAccount()
	{
		$user = User::find(config('app.user_id'));
		$user->forceDelete();

		dispatch(new CreateSiteAccountIfNotExists());

		$user = User::find(config('app.user_id'));

		$this->assertNotNull($user);

		foreach ($user->email_notification_setting->getFillableAll() as $name => $value) {
			$this->assertFalse($value);
		}
	}

	public function testNotificationEmailFound()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();
		$email->notice = true;
		$email->save();

		$like = factory(Like::class)
			->create();

		$notification = new NewLikeNotification($like);

		$this->assertEquals($email->email, $user->routeNotificationForMail($notification));
	}

	public function testNotificationEmailNotFound()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();
		$email->notice = false;
		$email->save();

		$like = factory(Like::class)
			->create();

		$notification = new NewLikeNotification($like);

		$this->assertNull($user->routeNotificationForMail($notification));
	}

	public function testSuspended()
	{
		$user = factory(User::class)
			->create();

		$this->assertFalse($user->isSuspended());

		$user->suspend();
		$user->refresh();

		$this->assertTrue($user->isSuspended());

		$user->unsuspend();
		$user->refresh();

		$this->assertFalse($user->isSuspended());
	}

	public function testFixedBlogPostLikeAuth()
	{
		$blog = factory(Blog::class)->states('fixed')->create();

		$like = factory(Like::class)->create([
			'likeable_type' => 'blog',
			'likeable_id' => $blog->id
		]);

		$blog->refresh();

		$this->assertTrue($blog->isFixed());
		$this->assertEquals(1, $blog->like_count);

		$response = $this->get(route('profile', ['user' => $blog->create_user]))
			->assertOk()
			->assertViewHas('top_blog_record', $blog);

		$top_blog_record = $response->viewData('top_blog_record');
		$this->assertEquals(0, $top_blog_record->likes->count());

		$response = $this->actingAs($like->create_user)
			->get(route('profile', ['user' => $blog->create_user]))
			->assertOk()
			->assertViewHas('top_blog_record', $blog);

		$top_blog_record = $response->viewData('top_blog_record');
		$this->assertEquals(1, $top_blog_record->likes->count());
	}

	public function testIsOkOpenCreatedBooks()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('users.books.created', ['user' => $user]))
			->assertOk();
	}
}
