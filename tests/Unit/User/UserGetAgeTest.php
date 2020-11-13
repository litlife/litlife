<?php

namespace Tests\Unit\User;

use App\User;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UserGetAgeTest extends TestCase
{
    public function test1()
    {
        Carbon::setTestNow('2020-01-02');

        $user = new User();
        $user->born_date = '2000-01-01';

        $this->assertEquals(20, $user->getAge());
    }

    public function test2()
    {
        Carbon::setTestNow('2019-12-31');

        $user = new User();
        $user->born_date = '2000-01-01';

        $this->assertEquals(19, $user->getAge());
    }

    public function testNull()
    {
        $user = new User();
        $user->born_date = null;

        $this->assertEquals(null, $user->getAge());
    }
}
