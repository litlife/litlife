<?php

namespace Tests\Feature\Book;

use App\Book;
use Tests\TestCase;

class BookCheckedTest extends TestCase
{


	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testStatus()
	{
		$book = factory(Book::class)->create();
		$book->statusPrivate();
		$book->save();

		$this->assertTrue($book->isPrivate());
		$this->assertTrue($book->is_private);
		$this->assertTrue($book->isStatus('Private'));

		$book->statusSentForReview();
		$book->save();

		$this->assertTrue($book->isSentForReview());
		$this->assertTrue($book->is_sent_for_review);
		$this->assertTrue($book->isStatus('OnReview'));

		$book->statusAccepted();
		$book->save();

		$this->assertTrue($book->isAccepted());
		$this->assertTrue($book->is_accepted);
		$this->assertTrue($book->isStatus('Accepted'));

		$book->statusReject();
		$book->save();

		$this->assertTrue($book->isRejected());
		$this->assertTrue($book->is_rejected);
		$this->assertTrue($book->isStatus('Rejected'));
	}
}
