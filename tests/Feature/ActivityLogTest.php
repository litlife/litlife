<?php

namespace Tests\Feature;

use App\Activity;
use App\Author;
use App\Book;
use App\User;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{


	public function testShowHttp()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.activity_logs', ['book' => $book]))
			->assertOk();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.activity_logs', ['user' => $user]))
			->assertOk();

		$author = factory(Author::class)
			->create();

		$this->actingAs($user)
			->get(route('authors.activity_logs', ['author' => $author]))
			->assertOk();
	}

	public function testShowBookDeleted()
	{
		$admin = factory(User::class)->states('admin')->create();

		$activity = factory(Activity::class)->create();

		$subject = $activity->subject;

		$subject->forceDelete();

		$this->actingAs($admin)
			->get(route('users.activity_logs', ['user' => $activity->causer]))
			->assertOk();
	}


}
