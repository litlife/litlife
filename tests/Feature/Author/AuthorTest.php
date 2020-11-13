<?php

namespace Tests\Feature\Author;

use App\Author;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    public function testFulltextSearch()
    {
        $author = Author::FulltextSearch('Время&—&детство!')->get();

        $this->assertTrue(true);
    }
}
