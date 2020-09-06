<?php

namespace Tests\Feature\Book\Cover;

use App\Attachment;
use App\Author;
use App\User;
use Tests\TestCase;

class BookRemoveCoverTest extends TestCase
{
	public function testDetachCover()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$attachment = factory(Attachment::class)->states('cover')->create();

		$book = $attachment->book;
		$book->sections_count = 10;
		$book->save();

		$this->assertTrue($attachment->isCover());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.remove_cover', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('attachment.cover_removed'));

		$attachment->refresh();
		$book->refresh();

		$this->assertFalse($attachment->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);
		/*
				$this->assertEquals(1, $book->activities()->count());
				$activity = $book->activities()->first();
				$this->assertEquals('set_cover', $activity->description);
				$this->assertEquals($user->id, $activity->causer_id);
				$this->assertEquals('user', $activity->causer_type);
		*/
	}

	public function testCantRemoveCoverIfBookForSale()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$cover = factory(Attachment::class)
			->states('cover')
			->create(['book_id' => $book->id]);

		$this->assertNotNull($book->fresh()->cover);
		$this->assertTrue($book->isForSale());

		$this->assertFalse($user->can('remove_cover', $book));
		$this->assertFalse($user->can('delete', $cover));

		$this->actingAs($user)
			->get(route('books.remove_cover', ['book' => $book]))
			->assertForbidden();
	}
}
