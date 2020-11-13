<?php

namespace Tests\Feature;

use Tests\TestCase;

class SidebarTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShow()
    {
        $this->get(route('sidebar.show'))
            ->assertOk()
            ->assertJson(['show_sidebar' => true])
            ->assertCookie('show_sidebar', true);
    }

    public function testHomeIfLatestRouteHome()
    {
        $this->get(route('sidebar.hide'))
            ->assertOk()
            ->assertJson(['show_sidebar' => false])
            ->assertCookie('show_sidebar', false);
    }

    public function testViewHasShowSidebarTrueIfCookieTrue()
    {
        $response = $this->withCookie('show_sidebar', true)
            ->get(route('home'))
            ->assertOk()
            ->assertViewHas('showSidebar', true);
    }

    public function testViewHasShowSidebarFalseIfCookieFalse()
    {
        $response = $this->withCookie('show_sidebar', false)
            ->get(route('home'))
            ->assertOk()
            ->assertViewHas('showSidebar', false);
    }
}
