<?php

namespace Tests\Feature;

use App\User;
use App\Variable;
use Illuminate\Support\Str;
use Tests\TestCase;

class SettingTest extends TestCase
{
	public function testSetForbiddenWords()
	{
		$word = uniqid();
		$word2 = '       ' . uniqid() . ' ' . uniqid();

		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->post(route('settings.save'), [
				'forbidden_words' => $word . "\r\n" . $word2
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$settings = Variable::where('name', 'settings')->first();

		$this->assertContains($word, $settings->value['forbidden_words']);
		$this->assertContains(trim($word2), $settings->value['forbidden_words']);
	}

	public function testSetForbiddenWordsEmptyLines()
	{
		$word = uniqid();
		$word2 = uniqid();

		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->post(route('settings.save'), [
				'forbidden_words' => $word . "\r\n   \r\n" . $word2
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$settings = Variable::where('name', 'settings')->first();

		$this->assertEquals([$word, $word2], $settings->value['forbidden_words']);
	}

	public function testCheckWordsInComments()
	{
		$word = Str::random(8);
		$word2 = '       ' . Str::random(8) . ' ' . Str::random(8);

		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->post(route('settings.save'), [
				'check_words_in_comments' => $word . "\r\n" . $word2
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$settings = Variable::where('name', 'settings')->first();

		$this->assertContains($word, $settings->value['check_words_in_comments']);
		$this->assertContains(trim($word2), $settings->value['check_words_in_comments']);

		$response = $this->actingAs($user)
			->get(route('settings.index'))
			->assertOk()
			->assertSeeText($word);
	}
}
