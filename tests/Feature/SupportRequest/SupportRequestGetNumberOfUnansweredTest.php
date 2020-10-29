<?php

namespace Tests\Feature\SupportRequest;

use App\User;
use Tests\TestCase;

class SupportRequestGetNumberOfUnsolvedTest extends TestCase
{
	public function test()
	{
		$user = factory(User::class)
			->create();

		$this->assertIsInt($user->getNumberOfUnsolved());
	}
}
