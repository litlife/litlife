<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Enums\StatusEnum;
use App\Section;
use Tests\TestCase;

class SectionCharactersCountTest extends TestCase
{
    public function testCharactersCountAttribute()
    {
        $value = rand(1, 1000);

        $section = new Section();

        $section->characters_count = $value;

        $this->assertEquals($value, $section->characters_count);
        $this->assertEquals($value, $section->character_count);
    }

    public function testSetContentCharactersCount()
    {
        $section = Section::factory()->create();
        $section->content = 'test';
        $section->save();
        $section->refresh();

        $book = $section->book;

        $this->assertEquals(1, $section->pages_count);
        $this->assertEquals(4, $section->characters_count);
        $this->assertEquals(4, $book->characters_count);
    }

    public function testGetCharacterCountInText()
    {
        $text = 'text text! text123';

        $this->assertEquals(16, (new Section())->getCharacterCountInText($text));

        $text = 'текст - текст ';

        $this->assertEquals(11, (new Section())->getCharacterCountInText($text));

        $text = "<i>текст \r\n текст</i>";

        $this->assertEquals(10, (new Section())->getCharacterCountInText($text));
    }

    public function testRefreshCharactersCount()
    {
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $section = Section::factory()->create();
        $section->content = $content;
        $section->save();

        $this->assertEquals((new Section())->getCharacterCountInText($content), $section->character_count);

        $section->character_count = 0;
        $section->save();
        $section->refresh();

        $this->assertEquals(0, $section->character_count);

        $section->refreshCharactersCount();
        $section->refresh();

        $this->assertEquals((new Section())->getCharacterCountInText($content), $section->character_count);
    }


    public function testBookCharactersCountAfterChangeStatus()
    {
        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();

        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $this->assertTrue($user->can('update', $book));

        $section = $book->sections()->chapter()->orderBy('id', 'desc')->first();

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content,
                    'status' => StatusEnum::Accepted,
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $book->refresh();
        $this->assertTrue($section->fresh()->isAccepted());

        $this->assertEquals((new Section())->getCharacterCountInText($content), $book->characters_count);

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content,
                    'status' => StatusEnum::Private,
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $book->refresh();
        $this->assertTrue($section->fresh()->isPrivate());

        $this->assertEquals(0, $book->characters_count);

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content,
                    'status' => StatusEnum::Accepted,
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $book->refresh();
        $this->assertTrue($section->fresh()->isAccepted());

        $this->assertEquals((new Section())->getCharacterCountInText($content), $book->characters_count);
    }

}
