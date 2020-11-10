<?php

namespace Tests\Feature\User;

use App\AuthorStatus;
use Tests\TestCase;

class UserAuthorsTest extends TestCase
{
	public function testListReadLaterHttpIsOk()
	{
		$author_status = AuthorStatus::factory()->read_later()->create();

		$this->actingAs($author_status->user)
			->get(route('users.authors.read_later', ['user' => $author_status->user]))
			->assertOk()
			->assertSeeText($author_status->author->name);
	}
}
