<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Section;
use App\User;
use Tests\TestCase;

class NoteEditTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEditPageIsOk()
    {
        $user = User::factory()
            ->admin()
            ->create();

        $note = Section::factory()
            ->accepted()
            ->note()
            ->for(Book::factory()->accepted())
            ->create();

        $book = $note->book;

        $this->assertTrue($note->isAccepted());
        $this->assertTrue($note->isNote());

        $this->actingAs($user)
            ->get('books/'.$book->id.'/notes/'.$note->inner_id.'/edit')
            ->assertOk();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdateIsOk()
    {
        $title = $this->faker->realText(100);
        $content = '<p>'.$this->faker->realText(100).'</p>';

        $user = User::factory()
            ->admin()
            ->create();

        $note = Section::factory()
            ->accepted()
            ->note()
            ->for(Book::factory()->accepted())
            ->create();

        $book = $note->book;

        $this->actingAs($user)
            ->patch('books/'.$book->id.'/notes/'.$note->inner_id.'', [
                'title' => $title,
                'content' => $content
            ])
            ->assertRedirect();

        $note->refresh();

        $this->assertEquals($title, $note->title);
        $this->assertEquals($content, $note->getContent());
    }
}
