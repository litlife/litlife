<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use App\User;
use Tests\TestCase;

class AttachmentSetAsCoverTest extends TestCase
{
	public function testSetAsCoverHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->states('administrator')->create();

		$attachment = factory(Attachment::class)->create();

		$book = $attachment->book;
		$book->sections_count = 10;
		$book->save();

		$this->assertFalse($attachment->isCover());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.attachments.set_cover', ['book' => $book, 'id' => $attachment->id]))
			->assertOk()
			->assertSeeText(__('attachment.selected_as_cover', ['name' => $attachment->name]));

		$attachment->refresh();
		$book->refresh();

		$this->assertTrue($attachment->isCover());
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
		$this->assertEquals($book->edit_user_id, $user->id);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('set_cover', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}
}
