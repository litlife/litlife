<?php

namespace Tests\Feature\Artisan;

use App\User;
use App\UserGroup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttachUserGroupWithUserStatusTest extends TestCase
{
	public function test()
	{
		$text = Str::random(10);
		$text2 = Str::random(10);

		$group = UserGroup::factory()->create(['name' => $text]);

		$user = User::factory()->create(['text_status' => $text2 . ',' . $text]);

		Artisan::call('user:attach_group_with_status', ['name' => $text]);

		$user->refresh();

		$this->assertEquals($text, $user->groups()->whereName($text)->first()->name);
		$this->assertEquals($text2, $user->text_status);
	}
}
