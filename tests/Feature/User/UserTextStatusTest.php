<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class UserTextStatusTest extends TestCase
{
	public function testAppendTextStatus()
	{
		$user = User::factory()->create();

		$value = $this->faker->sentence(2);

		$user->appendTextStatus($value);
		$user->save();
		$user->refresh();

		$this->assertEquals($value, $user->text_status);

		$value2 = $this->faker->sentence(2);

		$user->appendTextStatus($value2);
		$user->save();
		$user->refresh();

		$this->assertEquals($value . ', ' . $value2, $user->text_status);
	}

	public function testAppendUnique()
	{
		$user = User::factory()->create();

		$value = $this->faker->sentence(2);

		$user->appendTextStatus($value);
		$user->save();
		$user->refresh();

		$this->assertEquals($value, $user->text_status);

		$user->appendTextStatus($value);
		$user->save();
		$user->refresh();

		$this->assertEquals($value, $user->text_status);
	}

	public function testRemoveTextStatus()
	{
		$user = User::factory()->create();

		$value = 'текст1';
		$value2 = 'Текст2';

		$user->text_status = $value . ',' . $value2 . ',' . $value . ',' . $value2;
		$user->save();
		$user->refresh();

		$this->assertEquals($value . ', ' . $value2, $user->text_status);

		$user->removeTextStatus('текст2');
		$user->save();
		$user->refresh();

		$this->assertEquals($value, $user->text_status);

		$user->removeTextStatus('Текст1');
		$user->save();
		$user->refresh();

		$this->assertEquals('', $user->text_status);
	}

	public function testValueTrimed()
	{
		$user = User::factory()->create(['text_status' => 'текст1, текст2, Автор, текст5']);

		$user->removeTextStatus('Автор');
		$user->save();
		$user->refresh();

		$this->assertEquals('текст1, текст2, текст5', $user->text_status);
	}

	public function testHasTextStatus()
	{
		$user = User::factory()->create(['text_status' => 'текст1, текст2, Автор, текст5']);

		$this->assertTrue($user->hasTextStatus('текст1'));
		$this->assertTrue($user->hasTextStatus('текст2  '));
		$this->assertTrue($user->hasTextStatus('  текст5'));
		$this->assertTrue($user->hasTextStatus('Автор'));
		$this->assertFalse($user->hasTextStatus('текст4'));
		$this->assertTrue($user->hasTextStatus('автор'));
	}

	public function testWhereLike()
	{
		$text = uniqid();

		$user = User::factory()->create(['text_status' => 'текст1, текст2, ' . $text . ', текст5']);

		$this->assertEquals(1, User::whereTextStatusLike($text)->count());

		$text2 = uniqid();

		$this->assertEquals(0, User::whereTextStatusLike($text2)->count());
	}
}
