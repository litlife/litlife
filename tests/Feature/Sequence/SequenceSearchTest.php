<?php

namespace Tests\Feature\Sequence;

use App\Sequence;
use Illuminate\Support\Str;
use Tests\TestCase;

class SequenceSearchTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testIndexHttp()
	{
		$this->get(route('sequences'))
			->assertOk();
	}

	public function testSearchAjax()
	{
		$this->get(route('sequences'), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertOk();
	}

	public function testFullTextSearch()
	{
		$sequence = Sequence::factory()->create();
		$sequence->name = 'Время&—&детство!';
		$sequence->save();

		$sequence = Sequence::FulltextSearch($sequence->name)->get();

		$this->assertTrue(true);
	}

	public function testSearchNumberWithDotIsOk()
	{
		$response = $this->get(route('sequences.search', ['q' => '20.']))
			->assertOk();
	}

	public function testSearchByID()
	{
		$str = Str::random(10);

		$sequence = Sequence::factory()->create(['name' => $str]);

		$response = $this->get(route('sequences.search', ['q' => $sequence->id]))
			->assertOk()
			->assertJsonFragment([$sequence->name]);
	}

	public function testSearchByName()
	{
		$str = Str::random(10);

		$sequence = Sequence::factory()->create(['name' => $str]);

		$response = $this->get(route('sequences.search', ['q' => mb_substr($sequence->name, 0, 5)]))
			->assertOk()
			->assertJsonFragment([$sequence->name]);
	}
}
