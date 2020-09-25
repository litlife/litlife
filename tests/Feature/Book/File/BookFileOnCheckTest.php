<?php

namespace Tests\Feature\Book\File;

use App\BookFile;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class BookFileOnCheckTest extends TestCase
{
	public function testViewSendForReviewHttp()
	{
		BookFile::where('status', StatusEnum::OnReview)
			->delete();

		$admin = factory(User::class)->states('with_user_group')->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = factory(User::class)->create();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();

		$this->assertTrue($file->book->isAccepted());

		$this->actingAs($admin)
			->get(route('book_files.on_moderation'))
			->assertOk()
			->assertSessionHasNoErrors()
			->assertSee($file->extension);
	}
}
