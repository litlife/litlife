<?php

namespace Tests\Feature\Component;

use App\Book;
use App\View\Components\BookName;
use Tests\TestCase;

class BookNameComponentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNotFound()
    {
        $book = null;

        $component = new BookName($book, true, true);
        /*
                $this->assertEquals('<span>' . __('Book is not found') . '</span>', $component->render());
        */
        $this->assertEquals('<span>{{ $title }}</span>', $component->render());
        $this->assertEquals(__('Book is not found'), $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals('', $component->age);
        $this->assertEquals(false, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSoftDeleted()
    {
        $book = Book::factory()->create();
        $book->delete();

        $component = new BookName($book, true, false);
        /*
                $this->assertEquals('<span><a href="' . route('books.show', $book) . '">' . __('Book was deleted') . '</a></span>',
                    $component->render());
        */
        $this->assertEquals('<span><a href="{{ $href }}">{{ $title }}</a></span>', $component->render());
        $this->assertEquals(__('Book was deleted'), $component->title);
        $this->assertEquals(route('books.show', $book), $component->href);
        $this->assertEquals('', $component->age);
        $this->assertEquals(false, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHrefDisable()
    {
        $book = Book::factory()->create();

        $component = new BookName($book, false, false, false);
        /*
                $this->assertEquals('<span>' . $book->title . '</span>',
                    $component->render());
        */
        $this->assertEquals('<span>{{ $title }}</span>', $component->render());
        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(false, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadgeDisabled()
    {
        $book = Book::factory()->si_true()->lp_false()->create();

        $component = new BookName($book, false, 0, false);

        $this->assertEquals('<span>{{ $title }}</span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(0, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadgeSi()
    {
        $book = Book::factory()->si_true()->lp_false()->create();

        $component = new BookName($book, false, true, false);
        /*
                $this->assertEquals('<span>' .
                    $book->title .
                    ' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="' . __('book.is_si') . '">(' . __('book.si') . ')</span></span>',
                    $component->render());
        */
        $this->assertEquals('<span>{{ $title }} <span class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ __("book.is_si") }}">({{ __("book.si") }})</span></span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(1, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadgeLp()
    {
        $book = Book::factory()->lp_true()->si_false()->create();

        $component = new BookName($book, false, true, false);
        /*
                $this->assertEquals('<span>' .
                    $book->title .
                    ' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="' . __('book.is_lp') . '">(' . __('book.lp') . ')</span></span>',
                    $component->render());
        */
        $this->assertEquals('<span>{{ $title }} <span class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ __("book.is_lp") }}">({{ __("book.lp") }})</span></span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(1, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadgeIsCollection()
    {
        $book = Book::factory()->lp_false()->si_false()->create();
        $book->is_collection = true;
        $book->save();

        $component = new BookName($book, false, true, false);
        /*
                $this->assertEquals('<span>' .
                    $book->title .
                    ' <span class="text-muted text-lowercase">(' . __('book.is_collection') . ')</span></span>',
                    $component->render());
                */

        $this->assertEquals('<span>{{ $title }} <span class="text-muted text-lowercase">({{ __("book.is_collection") }})</span></span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(1, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadgeWithAge()
    {
        $book = Book::factory()->lp_false()->si_false()->create();
        $book->age = 18;
        $book->save();

        $component = new BookName($book, false, true, false);
        /*
                $this->assertEquals('<span>' .
                    $book->title .
                    ' <sup><span class="text-muted">' . $book->age . '+</span></sup></span>',
                    $component->render());
        */
        $this->assertEquals('<span>{{ $title }} <sup><span class="text-muted">{{ $age }}+</span></sup></span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(18, $component->age);
        $this->assertEquals(1, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadgeIsPrivate()
    {
        $book = Book::factory()->lp_false()->si_false()->private()->with_create_user()->create(['age' => 0]);

        $this->be($book->create_user);

        $component = new BookName($book, false, true, false);
        /*
                $this->assertEquals('<span>' .
                    $book->title .
                    ' <i class="fas fa-lock" data-toggle="tooltip" data-placement="top" title="' . __('book.private_tooltip') . '"></i></span>',
                    $component->render());
        */
        $this->assertEquals('<span>{{ $title }} <i class="fas fa-lock" data-toggle="tooltip" data-placement="top" title="{{ __("book.private_tooltip") }}"></i></span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(1, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNameIfDontHaveAccess()
    {
        $book = Book::factory()->private()->with_create_user()->create();

        $component = new BookName($book, false, false, false);
        /*
                $this->assertEquals('<span>' .
                    __('Access to the book is restricted') .
                    '</span>',
                    $component->render());
                */
        $this->assertEquals('<span>{{ $title }}</span>',
            $component->render());

        $this->assertEquals(__('Access to the book is restricted'), $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(false, $component->badge);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowEvenIfTrashed()
    {
        $book = Book::factory()->lp_false()->si_false()->create();
        $book->delete();

        $component = new BookName($book, false, true, true);
        /*
                $this->assertEquals('<span>' . $book->title . ' <span class="text-muted">(' . __('Book was deleted') . ')</span></span>', $component->render());
        */
        $this->assertEquals('<span>{{ $title }} <span class="text-muted">{{ __("Book was deleted") }}</span></span>',
            $component->render());

        $this->assertEquals($book->title, $component->title);
        $this->assertEquals(false, $component->href);
        $this->assertEquals(0, $component->age);
        $this->assertEquals(1, $component->badge);
    }
}
