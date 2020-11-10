<?php

namespace Tests\Feature\Author\Manager;

use App\Manager;
use Tests\TestCase;

class ManagerSentOnReviewAndManageableNotPrivateAndNotOnReviewScoreTest extends TestCase
{
	public function testFoundIfAuthorAccepted()
	{
		$manager = Manager::factory()->character_author()->on_review()->create();

		$author = $manager->manageable;
		$author->statusAccepted();
		$author->save();

		$count = Manager::where('id', $manager->id)
			->sentOnReviewAndManageableNotPrivateAndNotOnReview()
			->count();

		$this->assertEquals(1, $count);
	}

	public function testNotFoundIfAuthorSentForReview()
	{
		$manager = Manager::factory()->character_author()->on_review()->create();

		$author = $manager->manageable;
		$author->statusSentForReview();
		$author->save();

		$count = Manager::where('id', $manager->id)
			->sentOnReviewAndManageableNotPrivateAndNotOnReview()
			->count();

		$this->assertEquals(0, $count);
	}

	public function testNotFoundIfAuthorPrivate()
	{
		$manager = Manager::factory()->character_author()->on_review()->create();

		$author = $manager->manageable;
		$author->statusPrivate();
		$author->save();

		$count = Manager::where('id', $manager->id)
			->sentOnReviewAndManageableNotPrivateAndNotOnReview()
			->count();

		$this->assertEquals(0, $count);
	}
}
