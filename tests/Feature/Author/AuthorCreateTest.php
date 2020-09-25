<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
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
	}
}
