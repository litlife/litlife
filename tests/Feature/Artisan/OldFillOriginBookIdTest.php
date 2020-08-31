<?php

namespace Tests\Feature\Artisan;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OldFillOriginBookIdTest extends TestCase
{
	public function testCommand()
	{
		Artisan::call('to_new:fill_origin_book_id', ['lower_id' => 10]);

		$this->assertTrue(true);
	}
}
