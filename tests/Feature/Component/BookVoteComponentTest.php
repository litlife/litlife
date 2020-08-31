<?php

namespace Tests\Feature\Component;

use App\BookVote;
use Tests\TestCase;

class BookVoteComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function test2()
	{
		$vote = new BookVote();
		$vote->vote = 2;

		$component = new \App\View\Components\BookVote($vote);

		$this->assertEquals('<span class="badge badge-danger">2</span>', $component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function test8()
	{
		$vote = new BookVote();
		$vote->vote = 8;

		$component = new \App\View\Components\BookVote($vote);

		$this->assertEquals('<span class="badge badge-success">8</span>', $component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testNumber()
	{
		$component = new \App\View\Components\BookVote(5);

		$this->assertEquals('<span class="badge badge-secondary">5</span>', $component->render());
	}
}
