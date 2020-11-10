<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Book;
use App\Enums\AuthorEnum;
use App\Jobs\Author\UpdateAuthorBooksCount;
use Tests\TestCase;

class AuthorBooksPivotTest extends TestCase
{
	public function testType()
	{
		$book = Book::factory()->with_writer()->create();

		$author = Author::factory()->create();

		$translator = Author::factory()->create();

		$editor = Author::factory()->create();

		$compiler = Author::factory()->create();

		$illustrator = Author::factory()->create();

		$book->writers()->syncWithoutDetaching([$author->id]);
		$book->translators()->syncWithoutDetaching([$translator->id]);
		$book->editors()->syncWithoutDetaching([$editor->id]);
		$book->compilers()->syncWithoutDetaching([$compiler->id]);
		$book->illustrators()->syncWithoutDetaching([$illustrator->id]);

		UpdateAuthorBooksCount::dispatch($author);
		UpdateAuthorBooksCount::dispatch($translator);
		UpdateAuthorBooksCount::dispatch($editor);
		UpdateAuthorBooksCount::dispatch($compiler);
		UpdateAuthorBooksCount::dispatch($illustrator);

		$book->refresh();

		$this->assertEquals(2, $book->writers()->count());
		$this->assertNotNull($book->writers()->first()->pivot->created_at);
		$this->assertEquals(1, $book->translators()->count());
		$this->assertEquals(1, $book->editors()->count());
		$this->assertEquals(1, $book->compilers()->count());
		$this->assertEquals(1, $book->illustrators()->count());
		$this->assertNotNull($book->illustrators()->first()->pivot->created_at);

		$this->assertEquals(6, $book->authors()->count());

		$book->editors()->detach();

		$this->assertEquals(0, $book->editors()->count());
		$this->assertEquals(2, $book->writers()->count());

		$this->assertEquals(1, $illustrator->books_count);
		$this->assertEquals(1, $compiler->books_count);
		$this->assertEquals(1, $editor->books_count);
		$this->assertEquals(1, $translator->books_count);
		$this->assertEquals(1, $author->books_count);
	}

	public function testNew()
	{
		$book = Book::factory()->with_writer()->create();

		$author = Author::factory()->create();

		$translator = Author::factory()->create();

		$editor = Author::factory()->create();

		$book->writers()->syncWithoutDetaching([$author->id]);
		$book->translators()->syncWithoutDetaching([$translator->id]);
		$book->editors()->syncWithoutDetaching([$editor->id]);
		$book->refresh();

		$this->assertEquals(4, $book->authors()->get()->count());
		$this->assertEquals(4, $book->authors->count());
		$this->assertEquals(2, $book->getAuthorsWithType(AuthorEnum::Writer)->count());
		$this->assertEquals(1, $book->getAuthorsWithType(AuthorEnum::Translator)->count());
		$this->assertEquals(1, $book->getAuthorsWithType(AuthorEnum::Editor)->count());

		$book->setRelation('writes', $book->getAuthorsWithType(AuthorEnum::Writer));
		$book->setRelation('editors', $book->getAuthorsWithType(AuthorEnum::Editor));
		$book->setRelation('illustrators', $book->getAuthorsWithType(AuthorEnum::Illustrator));
		$book->setRelation('translators', $book->getAuthorsWithType(AuthorEnum::Translator));
		$book->setRelation('compilers', $book->getAuthorsWithType(AuthorEnum::Compiler));

		$this->assertEquals(2, $book->writers->count());
		$this->assertEquals(1, $book->translators->count());
		$this->assertEquals(1, $book->editors->count());
	}

	public function testBookAuthorPivotType()
	{
		$author = factory(Author::class)
			->states(['with_illustrated_book'])
			->create();

		$book = $author->illustrated_books()->first();

		$author = $book->illustrators()->first();

		$this->assertNotNull($author);
		$this->assertNotNull($author->pivot);
		$this->assertEquals(4, $author->pivot->type);
		$this->assertEquals(AuthorEnum::Illustrator, $author->pivot->type);
		$this->assertEquals('Illustrator', $author->pivot->getTypeKey());
	}
}
