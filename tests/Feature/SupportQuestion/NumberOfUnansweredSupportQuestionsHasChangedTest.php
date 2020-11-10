<?php

namespace Tests\Feature\SupportQuestion;

use App\Enums\CacheTags;
use App\Events\NumberOfUnsolvedSupportQuestionsHasChanged;
use App\Jobs\User\UpdateUserNumberInProgressQuestions;
use App\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class NumberOfUnansweredSupportQuestionsHasChangedTest extends TestCase
{
	public function test()
	{
		$user = User::factory()->create();

		$this->assertNotNull($user->getNumberInProgressQuestions());

		$this->assertNotNull(Cache::tags([CacheTags::NumberInProcessSupportQuestions])->get($user->id));

		UpdateUserNumberInProgressQuestions::dispatch($user);

		$this->assertNull(Cache::tags([CacheTags::NumberInProcessSupportQuestions])->get($user->id));
	}
}
