<?php

namespace Tests\Feature\SupportRequest;

use App\Enums\CacheTags;
use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class NumberOfUnsolvedSupportRequestsHasChangedTest extends TestCase
{
	public function test()
	{
		$user = factory(User::class)
			->create();

		$this->assertNotNull($user->getNumberOfUnsolved());

		$this->assertNotNull(Cache::tags([CacheTags::NumberOfUnsolvedRequests])->get($user->id));

		event(new NumberOfUnsolvedSupportRequestsHasChanged($user));

		$this->assertNull(Cache::tags([CacheTags::NumberOfUnsolvedRequests])->get($user->id));
	}
}
