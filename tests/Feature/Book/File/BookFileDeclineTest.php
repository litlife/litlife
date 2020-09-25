<?php

namespace Tests\Feature\Book\File;

use App\BookFile;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class BookFileDeclineTest extends TestCase
{
	public function testDeclineHttp()
	{
		config(['activitylog.enabled' => true]);

		BookFile::sentOnReview()
			->update(['status' => StatusEnum::Accepted]);

		$admin = factory(User::class)->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();

		BookFile::flushCachedOnModerationCount();
		$this->assertEquals(1, BookFile::getCachedOnModerationCount());

		$this->followingRedirects()
			->actingAs($admin)
			->get(route('book_files.decline', ['file' => $file]))
			->assertOk()
			->assertDontSeeText($file->extension)
			->assertSeeText(__('book_file.declined'));

		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$file->refresh();

		$this->assertTrue($file->isRejected());

		$this->get(route('books.show', $file->book))
			->assertDontSeeText($file->extension);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('declined', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}
}
