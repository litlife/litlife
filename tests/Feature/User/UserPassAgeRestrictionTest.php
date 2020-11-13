<?php

namespace Tests\Feature\User;

use App\Book;
use App\User;
use Tests\TestCase;

class UserPassAgeRestrictionTest extends TestCase
{
    public function testSetCookiePassAgeRestriction()
    {
        $age = 12;

        $user = User::factory()->create();

        $book = Book::factory()->create(['age' => 18]);

        $name = 'can_pass_age';

        $this->actingAs($user)
            ->get(route('user_pass_age_restriction', ['age' => $age]))
            ->assertOk()
            ->assertJson([$name => $age])
            ->assertCookie($name, $age)
            ->assertCookieNotExpired($name);
    }
}
