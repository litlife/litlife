<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Notifications\AuthorPageNeedsToBeVerifiedNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorCreateTest extends TestCase
{
	public function testCreateAuthorAverageRatingDBRecord()
	{
		$author = factory(Author::class)
			->create();

		$this->assertDatabaseHas('author_average_rating_for_periods', [
			'author_id' => $author->id
		]);
	}

	public function testWithBiographyHttp()
	{
		$user = factory(User::class)
			->create();

		$biography = $this->faker->realText(200);

		$response = $this->actingAs($user)
			->post(route('authors.store'),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male',
					'biography' => $biography
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$author = $user->created_authors()->first();

		$this->assertNotNull($author);

		$response = $this->get(route('authors.show', $author))
			->assertSeeText($biography);
	}

	public function testStoreHttp()
	{
		Notification::fake();

		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->post(route('authors.store'),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male'
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$author = $user->created_authors()->first();

		$this->assertNotNull($author);

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('created', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		Notification::assertNotSentTo($user, AuthorPageNeedsToBeVerifiedNotification::class);
	}

	public function testSentNotificationIfUserAndAuthorNameMatch()
	{
		Notification::fake();

		$lastName = Str::random(6);
		$firstName = Str::random(6);
		$nickName = Str::random(6);

		$user = factory(User::class)
			->create([
				'last_name' => $lastName,
				'first_name' => $firstName,
				'nick' => $nickName
			]);

		$authorNew = factory(Author::class)
			->make([
				'last_name' => $lastName,
				'first_name' => $firstName,
				'nick' => $nickName
			]);

		$response = $this->actingAs($user)
			->post(route('authors.store'),
				[
					'first_name' => $authorNew->first_name,
					'last_name' => $authorNew->last_name,
					'nick' => $authorNew->nick
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		Notification::assertSentTo($user, AuthorPageNeedsToBeVerifiedNotification::class);
	}
}
