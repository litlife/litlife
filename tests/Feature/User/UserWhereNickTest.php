<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserWhereNickTest extends TestCase
{
	public function testFoundHttp()
	{
		$user = User::factory()->create();

		$this->get(route('users.where.nick', ['nick' => $user->nick]))
			->assertOk()
			->assertSeeText($user->nick);
	}

	public function testSearchIgnoreCaseHttp()
	{
		$nick = Str::random(8);

		$user = User::factory()->create(['nick' => $nick]);

		$this->get(route('users.where.nick', ['nick' => mb_strtoupper($nick)]))
			->assertOk()
			->assertSeeText($user->nick);

		$this->get(route('users.where.nick', ['nick' => mb_strtolower($nick)]))
			->assertOk()
			->assertSeeText($user->nick);
	}

	public function testNotFoundHttp()
	{
		$nick = Str::random(8);

		$user = User::factory()->create(['nick' => $nick]);

		$this->get(route('users.where.nick', ['nick' => mb_substr($nick, -1)]))
			->assertOk()
			->assertDontSeeText($user->nick);
	}

	public function testDoubleSlash()
	{
		$nick = Str::random(8) . '\\';

		$user = User::factory()->create(['nick' => $nick]);

		$this->get(route('users.where.nick', ['nick' => $nick . '\\']))
			->assertOk()
			->assertDontSeeText($user->nick);
	}
}
