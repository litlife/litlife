<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Book;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SectionCreateTest extends TestCase
{
    public function testCreateHttp()
    {
        $user = User::factory()->create();
        $user->group->edit_self_book = true;
        $user->group->edit_other_user_book = true;
        $user->push();

        $book = Book::factory()->create();

        $this->actingAs($user)
            ->get(route('books.sections.create', ['book' => $book]))
            ->assertOk();
    }

    public function testStoreHttp()
    {
        Bus::fake(BookUpdatePageNumbersJob::class);

        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $user = User::factory()->create();
        $user->group->edit_self_book = true;
        $user->group->edit_other_user_book = true;
        $user->push();

        $book = Book::factory()->create();

        $this->actingAs($user)
            ->post(route('books.sections.store', ['book' => $book]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $section = $book->sections()->first();

        $book->refresh();

        $this->assertEquals($title, $section->title);
        $this->assertEquals($content, $section->getContent());
        $this->assertEquals(1, $book->fresh()->sections_count);
        $this->assertEquals((new Section())->getCharacterCountInText($content), $section->character_count);
        $this->assertEquals((new Section())->getCharacterCountInText($content), $book->characters_count);

        Bus::assertDispatched(BookUpdatePageNumbersJob::class);
    }

    public function testStoreChildHttp()
    {
        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $user = User::factory()->create();
        $user->group->edit_self_book = true;
        $user->group->edit_other_user_book = true;
        $user->push();

        $section = Section::factory()->create();

        $book = $section->book;
        $book->statusAccepted();
        $book->save();

        $this->actingAs($user)
            ->post(route('books.sections.store', ['book' => $book, 'parent' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $section->refresh();
        $book->refresh();

        $child_section = $section->children->first();

        $this->assertEquals($title, $child_section->title);
        $this->assertEquals($content, $child_section->getContent());
        $this->assertEquals((new Section())->getCharacterCountInText($content), $child_section->character_count);
        $this->assertEquals($book->characters_count, $child_section->character_count + $section->character_count);

        $this->assertTrue($section->isRoot());
        $this->assertTrue($child_section->isChildOf($section));
        $this->assertEquals(1, $section->children->count());

        $this->assertEquals(2, $book->fresh()->sections_count);
    }

    public function testCreateIfAuthorCanSaleHttp()
    {
        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();

        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $this->assertTrue($user->can('update', $book));

        $this->actingAs($user)
            ->post(route('books.sections.store', ['book' => $book]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $section = $book->sections()->orderBy('id', 'desc')->first();

        $this->assertEquals($title, $section->title);
        $this->assertEquals($content, $section->getContent());
        $this->assertTrue($section->isAccepted());
        /*
        $this->assertEquals($paid, $section->paid);
        $this->assertEquals($free_pages, $section->free_pages);
        */
    }

    public function testSeeOldContentIfMaxCharactersOverflowOnCreate()
    {
        config(['litlife.max_section_characters_count' => 3]);

        $user = User::factory()->admin()->create();

        $book = Book::factory()->create();

        $title = $this->faker->realText(50);
        $content = '<p>новый контент</p>';

        $this->actingAs($user)
            ->get(route('books.sections.create', ['book' => $book]))
            ->assertOk();

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('books.sections.store', ['book' => $book]), [
                'title' => $title,
                'content' => $content
            ])
            ->assertOk()
            ->assertSeeText('новый контент');
    }
}
