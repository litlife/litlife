<?php

namespace Tests\Unit\User;

use App\User;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UserGetJsonForYandexMetrikaTest extends TestCase
{
    public function test1()
    {
        Carbon::setTestNow('2020-01-01');

        $user = new User();
        $user->id = 10;
        $user->gender = 'male';
        $user->born_date = '2000-01-01';

        $this->assertEquals('{"age":20,"UserID":10,"gender":"male"}', $user->getJsonForYandexMetrika());
    }

    public function testGenderUnknown()
    {
        $user = new User();
        $user->id = 10;
        $user->born_date = null;

        $this->assertEquals('{"UserID":10,"gender":"unknown"}', $user->getJsonForYandexMetrika());
    }

    public function testNull()
    {
        $user = new User();

        $this->assertEquals('{}', $user->getJsonForYandexMetrika());
    }
}
