<?php

namespace Tests\Feature\Component;

use App\Author;
use App\View\Components\AuthorName;
use Tests\TestCase;

class AuthorNameComponentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDeleted()
    {
        $author = null;

        $component = new AuthorName($author);

        $this->assertEquals('{{ $name }}', $component->render());

        $this->assertEquals(__('Author is not found'), $component->name);
        $this->assertEquals(null, $component->lang);
        $this->assertEquals('', $component->class);
        $this->assertEquals(false, $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSoftDeleted()
    {
        $author = Author::factory()->create();
        $author->delete();

        $component = new AuthorName($author);

        $this->assertEquals('<a class="{{ $class }}" href="{{ $href }}">{{ $name }}</a>', $component->render());

        $this->assertEquals(__('Author deleted'), $component->name);
        $this->assertEquals(null, $component->lang);
        $this->assertEquals('author name', $component->class);
        $this->assertEquals(route('authors.show', $author), $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHrefEnable()
    {
        $author = Author::factory()->create(['lang' => 'EN']);

        $component = new AuthorName($author);
        /*
                $this->assertEquals('<a class="author name"  href="' . route('authors.show', $author) . '">' .
                    $author->last_name . ' ' . $author->first_name . ' ' . $author->middle_name . ' ' . $author->nickname
                    . '</a> (' . $author->lang . ')',
                    $component->render());
        */
        $this->assertEquals('<a class="{{ $class }}" href="{{ $href }}">{{ $name }}</a> ({{ $lang }})',
            $component->render());

        $this->assertEquals($author->last_name.' '.$author->first_name.' '.$author->middle_name.' '.$author->nickname, $component->name);
        $this->assertEquals('EN', $component->lang);
        $this->assertEquals('author name', $component->class);
        $this->assertEquals(route('authors.show', $author), $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHrefDisable()
    {
        $author = Author::factory()->create(['lang' => 'EN']);

        $component = new AuthorName($author, false);
        /*
                $this->assertEquals($author->last_name . ' ' . $author->first_name . ' ' . $author->middle_name . ' ' . $author->nickname . ' (' . $author->lang . ')',
                    $component->render());
                */
        $this->assertEquals('{{ $name }} ({{ $lang }})', $component->render());

        $this->assertEquals($author->last_name.' '.$author->first_name.' '.$author->middle_name.' '.$author->nickname, $component->name);
        $this->assertEquals('EN', $component->lang);
        $this->assertEquals('author name', $component->class);
        $this->assertEquals(false, $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLangRUDontShow()
    {
        $author = Author::factory()->create(['lang' => 'RU']);

        $component = new AuthorName($author);
        /*
                $this->assertEquals($author->last_name . ' ' . $author->first_name . ' ' . $author->middle_name . ' ' . $author->nickname . ' (' . $author->lang . ')',
                    $component->render());
                */
        $this->assertEquals('<a class="{{ $class }}" href="{{ $href }}">{{ $name }}</a>', $component->render());

        $this->assertEquals($author->last_name.' '.$author->first_name.' '.$author->middle_name.' '.$author->nickname, $component->name);
        $this->assertEquals('RU', $component->lang);
        $this->assertEquals('author name', $component->class);
        $this->assertEquals(route('authors.show', $author), $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowLockIfPrivate()
    {
        $author = Author::factory()->private()->create(['lang' => 'EN']);

        $component = new AuthorName($author);

        $this->assertEquals('<a class="{{ $class }}" href="{{ $href }}">{{ $name }}</a> ({{ $lang }}) <i class="fas fa-lock" data-toggle="tooltip" data-placement="top"
			   title="{{ __("book.private_tooltip") }}"></i>', $component->render());

        $this->assertEquals($author->last_name.' '.$author->first_name.' '.$author->middle_name.' '.$author->nickname, $component->name);
        $this->assertEquals('EN', $component->lang);
        $this->assertEquals('author name', $component->class);
        $this->assertEquals(route('authors.show', $author), $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowOnlineEnable()
    {
        $author = Author::factory()->with_author_manager()->create(['lang' => 'RU']);

        $component = new AuthorName($author, true, true);

        $this->assertEquals('<a class="{{ $class }}" href="{{ $href }}">{{ $name }}</a>', $component->render());

        $this->assertEquals($author->last_name.' '.$author->first_name.' '.$author->middle_name.' '.$author->nickname, $component->name);
        $this->assertEquals('RU', $component->lang);
        $this->assertEquals('online author name', $component->class);
        $this->assertEquals(route('authors.show', $author), $component->href);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowOnlineDisable()
    {
        $author = Author::factory()->with_author_manager()->create(['lang' => 'RU']);

        $component = new AuthorName($author, true, false);

        $this->assertEquals('<a class="{{ $class }}" href="{{ $href }}">{{ $name }}</a>', $component->render());

        $this->assertEquals($author->last_name.' '.$author->first_name.' '.$author->middle_name.' '.$author->nickname, $component->name);
        $this->assertEquals('RU', $component->lang);
        $this->assertEquals('author name', $component->class);
        $this->assertEquals(route('authors.show', $author), $component->href);
    }
}
