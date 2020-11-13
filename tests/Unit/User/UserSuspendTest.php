<?php

namespace Tests\Unit\User;

use App\User;
use PHPUnit\Framework\TestCase;

class UserSuspendTest extends TestCase
{
    public function testSuspended()
    {
        $user = new User();

        $this->assertFalse($user->isSuspended());

        $user->suspend();

        $this->assertTrue($user->isSuspended());

        $user->unsuspend();

        $this->assertFalse($user->isSuspended());
    }
}
