<?php

namespace Tests\Feature\Artisan;

use App\Author;
use App\AuthorBiography;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RefreshAllAuthorsBiographyTest extends TestCase
{
	public function testCommand()
	{
		$text = 'текст http://example.com текст';

		$authorBiography = factory(AuthorBiography::class)
			->create(['text' => $text]);

		$author = $authorBiography->author;

		Artisan::call('refresh:all_authors_biography', ['latest_id' => $author->id]);

		$authorBiography->refresh();

		$this->assertEquals('<p>текст <a href="/away?url=http%3A%2F%2Fexample.com">http://example.com</a> текст</p>', $authorBiography->text);
	}

	public function testAuthorWithoutBiography()
	{
		$author = factory(Author::class)
			->create();

		Artisan::call('refresh:all_authors_biography', ['latest_id' => $author->id]);

		$author->refresh();

		$this->assertNull($author->biography);
	}
}
