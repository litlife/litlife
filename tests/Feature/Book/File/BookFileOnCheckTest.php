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

		$admin = User::factory()->with_user_group()->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = User::factory()->create();

		$file = BookFile::factory()->txt()->create();
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
