<?php

namespace Tests\Browser;

use App\Book;
use Tests\DuskTestCase;

class ScrollToTopTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testScrollToTop()
    {
        $this->browse(function ($browser) {

            $book = Book::factory()->create();

            $browser->resize(300, 300)
                ->visit(route('books.show', ['book' => $book]));

            $scroll = $browser->script('return window.scrollY;');

            $this->assertEquals(0, pos($scroll));

            $browser->assertPresent('#back-to-top')
                ->assertMissing('#back-to-top');

            // scroll to the bottom
            $browser->script('$("html, body").animate({ scrollTop: $(document).height()-$(window).height() });');

            $browser->pause(1000);

            $scroll = $browser->script('return window.scrollY;');

            $this->assertTrue($scroll > 0);

            $browser->assertPresent('#back-to-top')
                ->assertVisible('#back-to-top');

            // scroll to the top
            $browser->click('#back-to-top');

            $browser->pause(1000);

            $scroll = $browser->script('return window.scrollY;');

            $this->assertEquals(0, pos($scroll));

            $browser->assertPresent('#back-to-top')
                ->assertMissing('#back-to-top');
        });
    }
}
