<?php

namespace Tests\Feature\Middleware;

use App\User;
use App\Variable;
use Tests\TestCase;

class ErrorIfForbiddenWordsExistsTest extends TestCase
{
	public function testForbiddenIfWordExists()
	{
		$word = uniqid();

		$this->addForbiddenWord($word);

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('users.blogs.store', ['user' => $user]), [
				'bb_text' => 'текст ' . $word
			])
			->assertForbidden();
	}

	public function addForbiddenWord($word)
	{
		$settings = Variable::where('name', 'settings')->first();

		if (empty($settings)) {
			$settings = new Variable();
			$settings->name = 'settings';
		}

		$array = $settings->value['forbidden_words'];
		$array[] = $word;

		$settings->value = ['forbidden_words' => $array];
		$settings->save();
	}

	public function testOkIfWordNotExists()
	{
		$word = uniqid();

		$user = factory(User::class)
			->states('with_user_permissions')
			->create();

		$response = $this->actingAs($user)
			->post(route('users.blogs.store', ['user' => $user]), [
				'bb_text' => 'текст ' . $word
			])
			->assertRedirect();
	}

	public function testForbiddenIfTwoWordsExists()
	{
		$word = uniqid();
		$word2 = uniqid();

		$this->addForbiddenWord($word);
		$this->addForbiddenWord($word2);

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('users.blogs.store', ['user' => $user]), [
				'bb_text' => 'текст ' . $word
			])
			->assertForbidden();

		$response = $this->actingAs($user)
			->post(route('users.blogs.store', ['user' => $user]), [
				'bb_text' => 'текст ' . $word2
			])
			->assertForbidden();
	}

	public function testOkIfForbiddenGetValue()
	{
		$word = uniqid();

		$this->addForbiddenWord($word);

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('home', ['value' => $word]))
			->assertOk();
	}

	public function testForbiddenWordIsUrl()
	{
		$word = 'https://bit.ly/2QibeQr';

		$this->addForbiddenWord($word);

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('users.blogs.store', ['user' => $user]), [
				'bb_text' => 'текст ' . $word
			])
			->assertForbidden();
	}
}
