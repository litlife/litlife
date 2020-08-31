<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Book;
use App\Jobs\Author\UpdateAuthorBooksCount;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthorDetectLangTest extends TestCase
{
	public function testUpdateLang()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['lang' => 'ES']);

		$book = factory(Book::class)
			->create(['ti_lb' => 'RU']);

		$book2 = factory(Book::class)
			->create(['ti_lb' => 'EN']);

		$book3 = factory(Book::class)
			->create(['ti_lb' => 'EN']);

		$author->written_books()->sync([$book->id]);
		$author->translated_books()->sync([$book2->id, $book3->id]);
		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('EN', $author->lang);
	}

	public function testUpdateLang2()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['lang' => 'ES']);

		$book = factory(Book::class)
			->create(['ti_lb' => 'RU']);

		$book2 = factory(Book::class)
			->create(['ti_lb' => 'RU']);

		$book3 = factory(Book::class)
			->create(['ti_lb' => 'EN']);

		$author->written_books()->sync([$book->id]);
		$author->translated_books()->sync([$book2->id, $book3->id]);
		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}

	public function testUpdateLang3()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['lang' => 'ES']);

		$book = factory(Book::class)
			->create(['ti_lb' => 'RU']);

		$book2 = factory(Book::class)
			->create(['ti_lb' => 'RU']);

		$book3 = factory(Book::class)
			->create(['ti_lb' => 'EN']);

		$author->written_books()->sync([$book->id]);
		$author->compiled_books()->sync([$book2->id, $book3->id]);
		$author->edited_books()->sync([$book2->id, $book3->id]);
		$author->illustrated_books()->sync([$book2->id, $book3->id]);
		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}

	public function testUpdateLangWithoutBooks()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['lang' => 'ES']);

		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('ES', $author->lang);
	}

	public function testRefreshLangIfExists()
	{
		$author = factory(Author::class)
			->create(['lang' => 'ES']);

		$this->assertEquals('ES', $author->lang);

		$book = factory(Book::class)
			->create(['ti_lb' => 'RU']);
		$author->written_books()->sync([$book->id]);
		$author->updateLang();
		$author->save();
		$author->refresh();

		$this->assertEquals('ES', $author->lang);

		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}

	public function testIfLangIsNullWithoutBooks()
	{
		$author = factory(Author::class)
			->create(['lang' => null]);

		$this->assertNull($author->lang);

		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('', $author->lang);
	}

	public function testIfLangIsNullWithBooks()
	{
		$author = factory(Author::class)
			->create(['lang' => null]);

		$this->assertNull($author->lang);

		$book = factory(Book::class)->create(['ti_lb' => 'RU']);
		$author->written_books()->sync([$book->id]);
		$author->save();
		$author->refresh();

		$author->updateLang();
		$author->save();
		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}

	public function testDetectCommand()
	{
		$author = factory(Author::class)
			->create(['lang' => null]);

		Artisan::call('author:detect_lang', ['limit' => 10]);

		$author->refresh();

		$this->assertNull($author->lang);

		$book = factory(Book::class)->create(['ti_lb' => 'RU']);
		$author->written_books()->sync([$book->id]);
		$author->save();
		UpdateAuthorBooksCount::dispatch($author);
		$author->refresh();
		$this->assertEquals(1, $author->books_count);

		Artisan::call('author:detect_lang', ['limit' => 10]);

		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}

	public function testIfAuthorPrivateAndBookPrivate()
	{
		$author = factory(Author::class)
			->states('private')
			->create(['lang' => 'ES']);

		$book = factory(Book::class)
			->create(['ti_lb' => 'RU']);

		$book2 = factory(Book::class)
			->states('private')
			->create(['ti_lb' => 'EN']);

		$book3 = factory(Book::class)
			->states('private')
			->create(['ti_lb' => 'EN']);

		$author->written_books()->sync([$book->id]);
		$author->translated_books()->sync([$book2->id, $book3->id]);
		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('EN', $author->lang);
	}

	public function testIfAuthorAcceptedAndBooksPrivate()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['lang' => 'ES']);

		$book = factory(Book::class)
			->states('accepted')
			->create(['ti_lb' => 'RU']);

		$book2 = factory(Book::class)
			->states('private')
			->create(['ti_lb' => 'EN']);

		$book3 = factory(Book::class)
			->states('private')
			->create(['ti_lb' => 'EN']);

		$author->written_books()->sync([$book->id]);
		$author->translated_books()->sync([$book2->id, $book3->id]);
		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}

	public function testIfAuthorAcceptedAndBooksSentForReview()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['lang' => 'ES']);

		$book = factory(Book::class)
			->states('sent_for_review')
			->create(['ti_lb' => 'RU']);

		$book2 = factory(Book::class)
			->states('private')
			->create(['ti_lb' => 'EN']);

		$book3 = factory(Book::class)
			->states('private')
			->create(['ti_lb' => 'EN']);

		$author->written_books()->sync([$book->id]);
		$author->translated_books()->sync([$book2->id, $book3->id]);
		$author->updateLang(true);
		$author->save();
		$author->refresh();

		$this->assertEquals('RU', $author->lang);
	}
}
