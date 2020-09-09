<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorPublishTest extends TestCase
{
	public function testMakeAccepted()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();
		$user->group->check_books = true;
		$user->push();

		$author = factory(Author::class)
			->states('sent_for_review')
			->create();

		$response = $this->actingAs($user)
			->followingRedirects()
			->get(route('authors.make_accepted', $author));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('author.published'))
			->assertDontSeeText(__('author.on_review_please_wait'));

		$author->refresh();

		$this->assertTrue($author->isAccepted());

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('make_accepted', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testPolicyMakeAccepted()
	{
		$user = factory(User::class)->create();

		$author = factory(Author::class)->create();
		$author->statusAccepted();
		$author->save();
		$user->refresh();

		$this->assertFalse($user->can('makeAccepted', $author));

		$user->group->check_books = true;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('makeAccepted', $author));

		$author->statusSentForReview();
		$author->save();
		$author->refresh();

		$this->assertTrue($user->can('makeAccepted', $author));
	}
}
