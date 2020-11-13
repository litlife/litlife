<?php

namespace Tests\Feature\Author;

use App\AuthorBiography;
use Tests\TestCase;

class AuthorBiographyTest extends TestCase
{
    public function testAutoParagraph()
    {
        $bio = AuthorBiography::factory()->create(['text' => '<p>текст</p>']);
        $bio->refresh();

        $this->assertEquals('<p>текст</p>', $bio->text);
    }

    public function testUrl()
    {
        $text = 'текст http://example.com текст';

        $authorBiography = AuthorBiography::factory()->create(['text' => $text]);

        $authorBiography->refresh();

        $this->assertEquals('<p>текст <a href="/away?url=http%3A%2F%2Fexample.com">http://example.com</a> текст</p>', $authorBiography->text);
    }
}
