<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Book;
use App\Enums\StatusEnum;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SectionEditTest extends TestCase
{
    public function testEditHttp()
    {
        $user = User::factory()->create();
        $user->group->edit_self_book = true;
        $user->group->edit_other_user_book = true;
        $user->push();

        $book = Book::factory()->with_section()->create();

        $section = $book->sections()->first();

        $this->actingAs($user)
            ->get(route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();
    }

    public function testUpdateHttp()
    {
        Bus::fake(BookUpdatePageNumbersJob::class);

        $user = User::factory()->create();
        $user->group->edit_self_book = true;
        $user->group->edit_other_user_book = true;
        $user->push();

        $book = Book::factory()->with_section()->create();

        $section = $book->sections()->first();

        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertRedirect(route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]))
            ->assertSessionHas(['success' => __('common.data_saved')]);

        $section->refresh();
        $book->refresh();

        $this->assertEquals($title, $section->title);
        $this->assertEquals($content, $section->getContent());
        $this->assertEquals((new Section())->getCharacterCountInText($content), $section->character_count);
        $this->assertEquals((new Section())->getCharacterCountInText($content), $book->characters_count);

        Bus::assertDispatched(BookUpdatePageNumbersJob::class);
    }

    public function testUpdateIfAuthorCanSaleHttp()
    {
        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();

        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $section = $book->sections()->orderBy('id', 'desc')->first();

        $this->assertTrue($user->can('update', $section));

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $section->refresh();

        $this->assertEquals($title, $section->title);
        $this->assertEquals($content, $section->getContent());
        /*
        $this->assertEquals($paid, $section->paid);
        $this->assertEquals($free_pages, $section->free_pages);
        */
    }

    public function testValidationMaxCharactersCountDontShow()
    {
        config(['litlife.max_section_characters_count' => 5]);

        $user = User::factory()->admin()->create();

        $section = Section::factory()->create();

        $book = $section->book;

        $title = $this->faker->realText(50);
        $content = '<p>1</p><p>2</p><p>3</p><p>4</p>';

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }

    public function testValidationMaxCharactersCountShow()
    {
        config(['litlife.max_section_characters_count' => 3]);

        $user = User::factory()->admin()->create();

        $section = Section::factory()->create();

        $book = $section->book;

        $title = $this->faker->realText(50);
        $content = '<p>1</p><p>2</p><p>3</p><p>4</p>';

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $title,
                    'content' => $content
                ])
            ->assertSessionHasErrors([
                'content' => __('validation.max.string',
                    [
                        'max' => config('litlife.max_section_characters_count'),
                        'attribute' => __('section.content')
                    ])
            ])
            ->assertRedirect();
    }

    public function testSeeOldContentIfMaxCharactersOverflowOnEdit()
    {
        config(['litlife.max_section_characters_count' => 3]);

        $user = User::factory()->admin()->create();

        $section = Section::factory()->create(['content' => 'старый контент']);

        $book = $section->book;

        $title = $this->faker->realText(50);
        $content = '<p>новый контент</p>';

        $this->actingAs($user)
            ->get(route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk()
            ->assertSeeText('старый контент');

        $this->actingAs($user)
            ->followingRedirects()
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]), [
                'title' => $title,
                'content' => $content
            ])
            ->assertOk()
            ->assertDontSeeText('старый контент')
            ->assertSeeText('новый контент');
    }

    public function testRefreshPrivateChaptersCountAfterUpdate()
    {
        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();

        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

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
        $this->assertEquals(1, $book->sections_count);
        $this->assertEquals(0, $book->private_chapters_count);

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
        $this->assertEquals(0, $book->sections_count);
        $this->assertEquals(1, $book->private_chapters_count);

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

        $this->assertEquals(1, $book->sections_count);
        $this->assertEquals(0, $book->private_chapters_count);
    }
}
