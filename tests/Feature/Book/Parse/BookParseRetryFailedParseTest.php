<?php

namespace Tests\Feature\Book\Parse;

use App\BookParse;
use App\User;
use Tests\TestCase;

class BookParseRetryFailedParseTest extends TestCase
{
    public function testRetryFailedParseHttp()
    {
        $admin = User::factory()->admin()->create();

        $parse = BookParse::factory()->failed()->create();

        $book = $parse->book;

        $this->actingAs($admin)
            ->get(route('books.retry_failed_parse', ['book' => $book]))
            ->assertRedirect();

        $book->refresh();
        $parse = $book->parse;

        $this->assertTrue($parse->isWait());
        $this->assertEquals($admin->id, $parse->create_user->id);
    }
}
