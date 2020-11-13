<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Section;
use App\User;
use Tests\TestCase;

class SectionBreakTest extends TestCase
{
    public function testEdit()
    {
        $content = '<p>текст первой главы</p>

<div class="u-empty-line">&nbsp;</div>

<hr class="u-section-break" />

<h5>Название второй главы</h5>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>

<hr class="u-section-break" />
<p><strong>Название третьей главы</strong></p>

<div class="u-empty-line">&nbsp;</div>
<p>текст третьей главы</p>';

        $user = User::factory()->admin()->create();

        $section = Section::factory()->create();

        $book = $section->book;

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $section->title,
                    'content' => $content
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $section->refresh();

        $section2 = $book->sections()->where('title', 'Название второй главы')->first();

        $this->assertEquals('Название второй главы', $section2->title);

        $this->assertEquals('<div class="u-empty-line"> </div><p>текст второй главы</p>',
            $section2->getContent());

        $section3 = $book->sections()->where('title', 'Название третьей главы')->first();

        $this->assertEquals('Название третьей главы', $section3->title);

        $this->assertEquals('<div class="u-empty-line"> </div><p>текст третьей главы</p>',
            $section3->getContent());
    }

    public function testUpdateHttpSplitOnPagesWithSectionBreak()
    {
        $page1_text = '';
        for ($a = 0; $a < 9; $a++) {
            $page1_text .= '<p>'.$this->faker->realText(100).'</p>';
        }

        $page2_text = '';
        for ($a = 0; $a < 9; $a++) {
            $page2_text .= '<p>'.$this->faker->realText(100).'</p>';
        }

        $page3_text = '';
        for ($a = 0; $a < 4; $a++) {
            $page3_text .= '<p>'.$this->faker->realText(100).'</p>';
        }

        $user = User::factory()->create();
        $user->group->edit_self_book = true;
        $user->group->edit_other_user_book = true;
        $user->push();

        $section = Section::factory()->create();

        $book = $section->book;
        $book->statusAccepted();
        $book->save();

        $this->assertEquals(1, $book->sections()->count());

        $section_content = $page1_text.'<div class="u-section-break"></div>'.$page2_text.'<div class="u-section-break"></div>'.$page3_text;

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $section->title,
                    'content' => $section_content
                ])
            ->assertRedirect();

        $section->refresh();
        $book->refresh();

        $book->setRelation('sections', Section::scoped(['book_id' => $book->id, 'type' => 'section'])
            ->defaultOrder()
            ->get());

        $this->assertEquals(3, $book->sections()->count());

        $this->assertEquals($page1_text, $book->sections[0]->getContent());
        $this->assertEquals((new Section())->getCharacterCountInText($page1_text), $book->sections[0]->character_count);

        $this->assertEquals($page2_text, $book->sections[1]->getContent());
        $this->assertEquals((new Section())->getCharacterCountInText($page2_text), $book->sections[1]->character_count);

        $this->assertEquals($page3_text, $book->sections[2]->getContent());
        $this->assertEquals((new Section())->getCharacterCountInText($page3_text), $book->sections[2]->character_count);

        $this->assertEquals(3, $book->fresh()->sections_count);
    }

    public function testUpdateHttpSplitOnPagesWithSectionBreakIfAuthorCanSale()
    {
        $page1_text = '';
        for ($a = 0; $a < 9; $a++) {
            $page1_text .= '<p>'.$this->faker->realText(100).'</p>';
        }

        $page2_text = '';
        for ($a = 0; $a < 9; $a++) {
            $page2_text .= '<p>'.$this->faker->realText(100).'</p>';
        }

        $page3_text = '';
        for ($a = 0; $a < 4; $a++) {
            $page3_text .= '<p>'.$this->faker->realText(100).'</p>';
        }

        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();

        $section = $book->sections()->chapter()->orderBy('id', 'desc')->first();

        $this->assertEquals(1, $book->sections()->chapter()->count());

        $section_content = $page1_text.'<div class="u-section-break"></div>'.$page2_text.'<div class="u-section-break"></div>'.$page3_text;

        $paid = boolval(rand(0, 1));
        $free_pages = rand(0, 10);

        $this->actingAs($user)
            ->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
                [
                    'title' => $section->title,
                    'content' => $section_content,
                    'paid' => $paid,
                    'free_pages' => $free_pages,
                ])
            ->assertRedirect();

        $section->refresh();
        $book->refresh();

        $book->sections = Section::scoped(['book_id' => $book->id, 'type' => 'section'])
            ->chapter()
            ->defaultOrder()
            ->get();

        $this->assertEquals(3, $book->sections()->chapter()->count());

        $this->assertEquals($page1_text, $book->sections[0]->getContent());

        $this->assertEquals($page2_text, $book->sections[1]->getContent());

        $this->assertEquals($page3_text, $book->sections[2]->getContent());

        $this->assertEquals(3, $book->fresh()->sections_count);
        /*
                $this->assertEquals($paid, $book->sections[0]->paid);
                $this->assertEquals($free_pages, $book->sections[0]->free_pages);

                $this->assertEquals($paid, $book->sections[1]->paid);
                $this->assertEquals($free_pages, $book->sections[1]->free_pages);

                $this->assertEquals($paid, $book->sections[2]->paid);
                $this->assertEquals($free_pages, $book->sections[2]->free_pages);
                */
    }

}