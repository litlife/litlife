<?php

namespace Tests\Feature\User;

use App\Http\Requests\StoreUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserPasswordValidationRuleTest extends TestCase
{
	public function testRequired()
	{
		config(['litlife.min_password_length' => 6]);

		$password = '';

		$validator = $this->validate($password);

		$this->assertTrue($validator->fails());

		$this->assertEquals(__('validation.required', ['attribute' => __('user.password')]),
			pos($validator->errors()->get('password')));
	}

	private function validate(string $password): \Illuminate\Validation\Validator
	{
		return Validator::make(
			[
				'password' => $password,
				'password_confirmation' => $password
			],
			(new StoreUser)->passwordRules(),
			[],
			__('user')
		);
	}

	public function testMinLength()
	{
		config(['litlife.min_password_length' => 10]);

		$password = Str::random(9);

		$validator = $this->validate($password);

		$this->assertTrue($validator->fails());

		$this->assertEquals(__('validation.min.string', ['attribute' => __('user.password'), 'min' => config('litlife.min_password_length')]),
			pos($validator->errors()->get('password')));
	}

	public function testMustContainUpperCaseLetters()
	{
		config(['litlife.min_password_length' => 6]);

		$password = mb_strtolower($this->getPassword());

		$validator = $this->validate($password);

		$this->assertTrue($validator->passes());
	}

	private function getPassword()
	{
		return 'Abc' . rand(1000, 2000);
	}

	public function testMustContainLowerCaseLetters()
	{
		config(['litlife.min_password_length' => 6]);

		$password = mb_strtoupper($this->getPassword());

		$validator = $this->validate($password);

		$this->assertTrue($validator->passes());
	}

	public function testMustContainNumbers()
	{
		config(['litlife.min_password_length' => 6]);

		$password = 'SDRbfgfgertDSFSERbGf';

		$validator = $this->validate($password);

		$this->assertTrue($validator->fails());

		$this->assertEquals(__('validation.regex', ['attribute' => __('user.password')]),
			pos($validator->errors()->get('password')));
	}

	public function testValid()
	{
		config(['litlife.min_password_length' => 6]);

		$password = 'SDRbfgfgertDSFSERbGf34';

		$validator = $this->validate($password);

		$this->assertFalse($validator->fails());

		$this->assertEmpty(pos($validator->errors()->get('password')));
	}

	public function testRuLangLetters()
	{
		config(['litlife.min_password_length' => 6]);

		$password = 'aбвгд56AБ';

		$validator = $this->validate($password);

		$this->assertFalse($validator->fails());

		$this->assertEmpty(pos($validator->errors()->get('password')));
	}

	public function testOtherLangLetters()
	{
		config(['litlife.min_password_length' => 6]);

		$password = '測試測試測試測試5';

		$validator = $this->validate($password);

		$this->assertFalse($validator->fails());

		$this->assertEmpty(pos($validator->errors()->get('password')));
	}
}
