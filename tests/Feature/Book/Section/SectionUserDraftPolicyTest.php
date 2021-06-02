<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use Tests\TestCase;

class SectionUserDraftPolicyTest extends TestCase
{
    public function testUseDraftIfNotSectionTypePolicy()
    {
        $book = Book::factory()->with_author_manager()->with_section()->create();

        $section = $book->sections()->first();
        $section->type = 'note';
        $section->push();

        $author = $book->authors->first();
        $user = $author->managers()->first()->user;

        $this->assertFalse($user->can('use_draft', $section));
    }

    public function testUseDraftIfManagerNotAuthorPolicy()
    {
        $book = Book::factory()->with_author_manager()->with_section()->create();

        $section = $book->sections()->first();

        $author = $book->authors->first();
        $manager = $author->managers()->first();
        $manager->character = 'editor';
        $manager->push();

        $user = $manager->user;

        $this->assertFalse($user->can('use_draft', $section));
    }

    public function testUseDraftIfManagerAuthorPolicy()
    {
        $book = Book::factory()->with_author_manager()->with_section()->si_true()->create();

        $section = $book->sections()->first();
        $author = $book->authors->first();
        $manager = $author->managers()->first();

        $user = $manager->user;

        $this->assertTrue($user->can('use_draft', $section));
    }
}
