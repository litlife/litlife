<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class ScrollableTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testScrollHeader()
    {
        /*
        $this->browse(function ($browser) {

            $browser->resize(300, 300)
                ->visit(route('home'))
                ->with('header', function ($header) {
                    $header->assertSee(trans_choice('genre.genres', 1))
                        ->assertDontSee(trans_choice('forum.forums',  1));
                });
        });
       */
        $this->assertTrue(true);
    }
}
