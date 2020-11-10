<?php

namespace Tests\Feature;

use App\Book;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class StatusTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testEnum()
	{
		$this->assertEquals(0, StatusEnum::Accepted);
		$this->assertEquals(1, StatusEnum::OnReview);
		$this->assertEquals(2, StatusEnum::Rejected);
		$this->assertEquals(3, StatusEnum::Private);
		$this->assertEquals(4, StatusEnum::ReviewStarts);
	}

	public function testChangeStatus()
	{
		$user = User::factory()->create();

		$this->be($user);

		$book = Book::factory()->create([
				'status' => '0',
				'status_changed_at' => null,
				'status_changed_user_id' => null
			]);

		$this->assertEquals($book->status, 0);
		$this->assertEquals($book->status_changed_at, null);
		$this->assertEquals($book->status_changed_user_id, null);

		$book->changeStatus('OnReview');

		$this->assertEquals(StatusEnum::OnReview, $book->status);
		$this->assertNotNull($book->status_changed_at);
		$this->assertEquals($user->id, $book->status_changed_user_id);
	}

	public function testAccepted()
	{
		$book = Book::factory()->create();

		$book->statusAccepted();

		$this->assertTrue($book->isAccepted());
		$this->assertTrue($book->isStatus('Accepted'));
		$this->assertTrue($book->is_accepted);
	}

	public function testSentForReview()
	{
		$book = Book::factory()->create();

		$book->statusSentForReview();

		$this->assertTrue($book->isSentForReview());
		$this->assertTrue($book->isStatus('OnReview'));
		$this->assertTrue($book->is_sent_for_review);
	}

	public function testPrivate()
	{
		$book = Book::factory()->create();

		$book->statusPrivate();

		$this->assertTrue($book->isPrivate());
		$this->assertTrue($book->isStatus('Private'));
		$this->assertTrue($book->is_private);
	}

	public function testRejected()
	{
		$book = Book::factory()->create();

		$book->statusReject();

		$this->assertTrue($book->isRejected());
		$this->assertTrue($book->isStatus('Rejected'));
		$this->assertTrue($book->is_rejected);
	}

	public function testReviewStarts()
	{
		$book = Book::factory()->create();

		$book->statusReviewStarts();

		$this->assertTrue($book->isReviewStarts());
		$this->assertTrue($book->isStatus('ReviewStarts'));
		$this->assertTrue($book->is_review_starts);
	}

	public function testScopeWhereStatus()
	{
		$book = Book::factory()->create();
		$book->statusAccepted();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->whereStatus('Accepted')->count());
		$this->assertEquals(0, Book::where('id', $book->id)->whereStatus('Rejected')->count());
	}

	public function testScopeWhereStatusIn()
	{
		$book = Book::factory()->create();
		$book->statusAccepted();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->whereStatusIn(['Accepted', 'OnReview'])->count());
		$this->assertEquals(0, Book::where('id', $book->id)->whereStatusIn(['Rejected', 'ReviewStarts'])->count());
	}

	public function testScopeWhereStatusNot()
	{
		$book = Book::factory()->create();
		$book->statusAccepted();
		$book->save();

		$this->assertEquals(0, Book::where('id', $book->id)->whereStatusNot('Accepted')->count());
		$this->assertEquals(1, Book::where('id', $book->id)->whereStatusNot('Rejected')->count());
	}

	public function testScopeAccepted()
	{
		$book = Book::factory()->create();
		$book->statusAccepted();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->accepted()->count());
		$this->assertEquals(0, Book::where('id', $book->id)->sentOnReview()->count());
	}

	public function testScopeSentOnReview()
	{
		$book = Book::factory()->create();
		$book->statusSentForReview();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->sentOnReview()->count());
		$this->assertEquals(0, Book::where('id', $book->id)->accepted()->count());

		$book->statusReviewStarts();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->sentOnReview()->count());
		$this->assertEquals(0, Book::where('id', $book->id)->accepted()->count());
	}

	public function testScopePrivate()
	{
		$book = Book::factory()->create();
		$book->statusPrivate();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->private()->count());
		$this->assertEquals(0, Book::where('id', $book->id)->accepted()->count());
		$this->assertEquals(0, Book::where('id', $book->id)->sentOnReview()->count());
	}

	public function testScopeUnaccepted()
	{
		$book = Book::factory()->create();
		$book->statusPrivate();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->unaccepted()->count());

		$book->statusReviewStarts();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->unaccepted()->count());

		$book->statusSentForReview();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->unaccepted()->count());

		$book->statusAccepted();
		$book->save();

		$this->assertEquals(0, Book::where('id', $book->id)->unaccepted()->count());
	}

	public function testScopeAcceptedAndSentForReview()
	{
		$book = Book::factory()->create();
		$book->statusPrivate();
		$book->save();

		$this->assertEquals(0, Book::where('id', $book->id)->acceptedAndSentForReview()->count());

		$book->statusReviewStarts();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->acceptedAndSentForReview()->count());

		$book->statusSentForReview();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->acceptedAndSentForReview()->count());

		$book->statusAccepted();
		$book->save();

		$this->assertEquals(1, Book::where('id', $book->id)->acceptedAndSentForReview()->count());
	}

	public function testScopeAcceptedOrBelongsToUser()
	{
		$book = Book::factory()->with_create_user()->create();
		$book->statusPrivate();
		$book->save();

		$book2 = Book::factory()->with_create_user()->create();
		$book2->statusAccepted();
		$book2->save();

		$this->assertEquals(1, Book::where('id', $book->id)->acceptedOrBelongsToUser($book->create_user)->count());
		$this->assertEquals(1, Book::where('id', $book2->id)->acceptedOrBelongsToUser($book2->create_user)->count());

		$this->assertEquals(0, Book::where('id', $book->id)->acceptedOrBelongsToUser($book2->create_user)->count());
		$this->assertEquals(1, Book::where('id', $book2->id)->acceptedOrBelongsToUser($book->create_user)->count());
	}

	public function testScopeAcceptedOrBelongsToAuthUser()
	{
		$book = Book::factory()->with_create_user()->create();
		$book->statusPrivate();
		$book->save();

		$book2 = Book::factory()->with_create_user()->create();
		$book2->statusAccepted();
		$book2->save();

		$this->be($book->create_user);

		$this->assertEquals(1, Book::where('id', $book->id)->acceptedOrBelongsToAuthUser()->count());
		$this->assertEquals(1, Book::where('id', $book2->id)->acceptedOrBelongsToAuthUser()->count());

		$this->be($book2->create_user);

		$this->assertEquals(0, Book::where('id', $book->id)->acceptedOrBelongsToAuthUser()->count());
		$this->assertEquals(1, Book::where('id', $book2->id)->acceptedOrBelongsToAuthUser()->count());
	}

}
