<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidationRulesTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testAlphaAtLeastThreeSymbols()
	{
		$this->assertTrue(Validator::make(['test' => '...gh'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertTrue(Validator::make(['test' => 'gh'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertTrue(Validator::make(['test' => '...gh'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertTrue(Validator::make(['test' => 'u1$$$$'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertFalse(Validator::make(['test' => '...ghj.......'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertFalse(Validator::make(['test' => '%%%u12'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertFalse(Validator::make(['test' => '%%%u123'], ['test' => 'alnum_at_least_three_symbols'])->fails());

		$this->assertFalse(Validator::make(['test' => '%%%123'], ['test' => 'alnum_at_least_three_symbols'])->fails());
	}

	public function testDoesNotContainUrl()
	{
		$this->assertTrue(Validator::make(['test' => '...www.url.test'], ['test' => 'does_not_contain_url'])->fails());

		$this->assertTrue(Validator::make(['test' => '...www.url-9.test'], ['test' => 'does_not_contain_url'])->fails());

		$this->assertTrue(Validator::make(['test' => '...www.url-9.url-9.url-9.test'], ['test' => 'does_not_contain_url'])->fails());

		$this->assertTrue(Validator::make(['test' => '...www. url.test'], ['test' => 'does_not_contain_url'])->fails());

		$this->assertTrue(Validator::make(['test' => '...www.url.h5'], ['test' => 'does_not_contain_url'])->fails());

		$this->assertTrue(Validator::make(['test' => 'test www.url.d'], ['test' => 'does_not_contain_url'])->fails());

		$this->assertFalse(Validator::make(['test' => 'test www .url.d'], ['test' => 'does_not_contain_url'])->fails());
	}
}
