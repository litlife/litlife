<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\User;
use Tests\TestCase;

class SectionViewSectionListPolicyTest extends TestCase
{
    public function testFalseIfBookNotParsedAndBookHasOnlyDescription()
    {
        $book = Book::factory()->accepted()->with_section()->create();
        $book->parse->start();
        $book->push();

        $user = User::factory()->create();

        $this->assertFalse($user->can('view_section_list', $book));
    }

    public function testTrueIfBookPrivateAndUserCreator()
    {
        $book = Book::factory()->private()->with_create_user()->create();

        $user = $book->create_user;

        $this->assertTrue($user->can('view_section_list', $book));
    }

    public function testFalseIfBookPrivateAndUserNotCreator()
    {
        $book = Book::factory()->private()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('view_section_list', $book));
    }
}