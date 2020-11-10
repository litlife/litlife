<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use App\User;
use Tests\TestCase;

class BookFileEditTest extends TestCase
{
	public function testUpdateHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = User::factory()->admin()->create();

		$file = BookFile::factory()->txt()->create();

		$comment = $this->faker->realText(200);
		$number = rand(1, 100);

		$this->actingAs($admin)
			->followingRedirects()
			->patch(route('books.files.update', ['book' => $file->book, 'file' => $file->id]), [
				'comment' => $comment,
				'number' => $number
			])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$file->refresh();

		$this->assertEquals($comment, $file->comment);
		$this->assertEquals($number, $file->number);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('updated', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testCommentIsRequiredIfOtherFileWithSameExtensionExists()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = BookFile::factory()->txt()->create();

		$file2 = BookFile::factory()->txt()->create();

		$this->actingAs($user)
			->patch(route('books.files.update', compact('book', 'file')),
				[
					'comment' => ''
				]
			)
			->assertSessionHasErrors(['comment' => __('validation.required', ['attribute' => __('book_file.comment')])])
			->assertRedirect();
	}

	public function testCommentIsNotRequiredIfOtherFileWithDifferentExtensionExists()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = BookFile::factory()->odt()->create();

		$file2 = BookFile::factory()->txt()->create();

		$this->actingAs($user)
			->patch(route('books.files.update', compact('book', 'file')),
				[
					'comment' => ''
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();
	}
}
