<?php

namespace Tests\Feature\Book\Keyword;

use App\BookKeyword;
use App\User;
use Tests\TestCase;

class BookKeywordVotePolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$bookKeyword = BookKeyword::factory()->accepted()->create();

		$user = User::factory()->create();
		$user->group->book_keyword_vote = true;
		$user->save();

		$this->assertTrue($user->can('vote', $bookKeyword));
	}

	public function testCantIfNoPermission()
	{
		$bookKeyword = BookKeyword::factory()->accepted()->create();

		$user = User::factory()->create();
		$user->group->book_keyword_vote = false;
		$user->save();

		$this->assertFalse($user->can('vote', $bookKeyword));
	}
}
