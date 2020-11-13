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
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $this->actingAs($user)
            ->get(route('books.activity_logs', ['book' => $book]))
            ->assertOk();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.activity_logs', ['user' => $user]))
            ->assertOk();

        $author = Author::factory()->create();

        $this->actingAs($user)
            ->get(route('authors.activity_logs', ['author' => $author]))
            ->assertOk();
    }

    public function testShowBookDeleted()
    {
        $admin = User::factory()->admin()->create();

        $activity = Activity::factory()->create();

        $subject = $activity->subject;

        $subject->forceDelete();

        $this->actingAs($admin)
            ->get(route('users.activity_logs', ['user' => $activity->causer]))
            ->assertOk();
    }


}
