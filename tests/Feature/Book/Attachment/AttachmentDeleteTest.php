<?php

namespace Tests\Feature\Book\Attachment;

use App\Book;
use App\User;
use Tests\TestCase;

class AttachmentDeleteTest extends TestCase
{
	public function testDeleteHttp()
	{
		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)
			->states('with_cover', 'with_section')
			->create();

		$cover = $book->cover;

		$this->assertTrue($cover->isCover());

		$response = $this->actingAs($user)
			->delete(route('books.attachments.delete', ['book' => $book, 'id' => $cover]), [],
				['HTTP_X-Requested-With' => 'XMLHttpRequest']);

		$book->refresh();
		$cover->refresh();

		$response->assertOk()
			->assertJson($cover->toArray());

		$this->assertSoftDeleted($cover);
		$this->assertTrue($cover->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);
	}

	public function testRestoreHttp()
	{
		$user = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)
			->states('with_cover', 'with_section')
			->create();

		$cover = $book->cover;

		$this->assertTrue($cover->isCover());

		$cover->delete();

		$response = $this->actingAs($user)
			->delete(route('books.attachments.delete', ['book' => $book, 'id' => $cover]), [],
				['HTTP_X-Requested-With' => 'XMLHttpRequest']);

		$book->refresh();
		$cover->refresh();

		$response->assertOk()
			->assertJson($cover->toArray());

		$this->assertFalse($cover->trashed());
		$this->assertTrue($cover->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);
	}
}
