<?php

namespace Tests\Feature\Book\Keyword;

use App\BookKeyword;
use App\User;
use Tests\TestCase;

class BookKeywordIndexTest extends TestCase
{
	public function testShowBookKeywordsIfBookDeleted()
	{
		$bookKeyword = BookKeyword::factory()->create();

		$book = $bookKeyword->book;

		$book->delete();

		$user = User::factory()->admin()->create();

		$response = $this->actingAs($user)
			->get(route('books.keywords.index', ['book' => $book]))
			->assertOk();
	}

	public function testShowBookEditIfKeywordDeleted()
	{
		$bookKeyword = BookKeyword::factory()->create();

		$book = $bookKeyword->book;

		$bookKeyword->keyword->delete();

		$user = User::factory()->admin()->create();

		$response = $this->actingAs($user)
			->get(route('books.edit', ['book' => $book]))
			->assertOk();

		$response = $this->actingAs($user)
			->get(route('books.keywords.index', ['book' => $book]))
			->assertOk();
	}
}
