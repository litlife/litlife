<?php

namespace Tests\Feature\Book\Keyword;

use App\BookKeyword;
use App\Keyword;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookKeywordSearchTest extends TestCase
{
	public function testSearchHttp()
	{
		$book_keyword = factory(BookKeyword::class)->create();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => $book_keyword->keyword->text])
			->assertOk();

		$response->assertJsonFragment(['text' => $book_keyword->keyword->text]);
	}

	public function testSearchPartWordHttp()
	{
		$text = Str::random(8);

		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->keyword->text = $text;
		$book_keyword->push();
		$book_keyword->refresh();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => mb_substr($text, 1)])
			->assertOk();

		$response->assertJsonFragment(['text' => $book_keyword->keyword->text]);
	}

	public function testSearchDeletedKeywordHttp()
	{
		$text = Str::random(8);

		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->keyword->text = $text;
		$book_keyword->push();

		$book_keyword->keyword->delete();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => $book_keyword->keyword->text])
			->assertOk();

		$response->assertJsonMissing(['text' => $book_keyword->keyword->text]);
	}

	public function testSearchDeletedBookKeywordHttp()
	{
		$keyword = factory(Keyword::class)->create();

		$keyword->delete();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => $keyword->text])
			->assertOk();

		$response->assertJsonMissing(['text' => $keyword->text]);
	}
}
