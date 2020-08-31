<?php

namespace Tests\Browser;

use App\Attachment;
use App\Book;
use App\Enums\StatusEnum;
use Tests\DuskTestCase;

class AttachmentTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testCreate()
	{
		$this->browse(function ($browser) {

			$book = factory(Book::class)->create([
				'status' => StatusEnum::Private
			]);

			// create
			$browser->resize(1000, 1000)
				->loginAs($book->create_user)
				->visit(route('books.attachments.index', $book))
				->attach('file', __DIR__ . '/images/test.jpeg')
				->press(__('common.upload'))
				->assertSee(__('attachment.uploaded'));

			$attachment = $book->attachments->first();

			$browser->assertVisible('.attachments')
				->with('.attachments', function ($attachments) use ($attachment) {
					$this->assertEquals($attachments->attribute('.item[data-id="' . $attachment->id . '"]', 'data-url'), $attachment->url);
					$this->assertEquals($attachments->attribute('.item[data-id="' . $attachment->id . '"] img', 'src'), $attachment->fullUrlMaxSize(300, 300));
				});
		});
	}

	public function testDeleteAndRestore()
	{
		$this->browse(function ($browser) {

			$book = factory(Book::class)->create([
				'status' => StatusEnum::Private
			]);

			$attachment = new Attachment;
			$attachment->openImage(__DIR__ . '/images/test.jpeg');
			$attachment->storage = config('filesystems.default');
			$book->attachments()->save($attachment);
			$attachment = $attachment->fresh();

			// delete
			$browser->resize(1000, 1000)
				->loginAs($book->create_user)
				->visit(route('books.attachments.index', $book));

			$browser->assertVisible('.attachments')
				->with('.attachments .item[data-id="' . $attachment->id . '"]', function ($item) use ($attachment) {

					$item->press('button[data-toggle="dropdown"]');
					$dropdown_id = $item->attribute('button[data-toggle="dropdown"]', 'id');

					$item->with('[aria-labelledby="' . $dropdown_id . '"]', function ($dropdown_menu) {
						$dropdown_menu->assertSee(mb_strtolower(__('common.delete')))
							->assertDontSee(mb_strtolower(__('common.restore')))
							->click('.delete');
					});

					$item->waitFor('img.transparency')
						->waitFor('.title.transparency');

					$this->assertTrue($attachment->fresh()->trashed());

					$item->press('button[data-toggle="dropdown"]');

					$item->with('[aria-labelledby="' . $dropdown_id . '"]', function ($dropdown_menu) {
						$dropdown_menu->assertSee(mb_strtolower(__('common.restore')))
							->assertDontSee(mb_strtolower(__('common.delete')))
							->click('.restore');
					});

					$item->waitUntilMissing('img.transparency')
						->waitUntilMissing('.title.transparency');

					$this->assertFalse($attachment->fresh()->trashed());
				});
		});

	}

	public function testAttachCoverAndDetach()
	{
		$this->browse(function ($browser) {

			$book = factory(Book::class)->create([
				'status' => StatusEnum::Private
			]);

			$attachment = new Attachment;
			$attachment->openImage(__DIR__ . '/images/test.jpeg');
			$attachment->storage = config('filesystems.default');
			$book->attachments()->save($attachment);
			$attachment = $attachment->fresh();

			$this->assertNull($book->cover()->first());

			// attach
			$browser->resize(1000, 1000)
				->loginAs($book->create_user)
				->visit(route('books.attachments.index', $book))
				->assertVisible('.attachments')
				->with('.attachments .item[data-id="' . $attachment->id . '"]', function ($item) use ($attachment) {

					$item->press('button[data-toggle="dropdown"]');
					$dropdown_id = $item->attribute('button[data-toggle="dropdown"]', 'id');

					$item->with('[aria-labelledby="' . $dropdown_id . '"]', function ($dropdown_menu) {
						$dropdown_menu->assertSee(__('attachment.set_as_cover'))
							->clickLink(__('attachment.set_as_cover'));
					});
				})
				->assertSee(__('attachment.selected_as_cover', ['name' => $attachment->name]))
				->visit(route('books.edit', $book))
				->with('form', function ($form) use ($attachment) {
					$this->assertEquals($form->attribute('img', 'src'), $attachment->fullUrlMaxSize(200, 200, 90));
				});

			$book = Book::any()->findOrFail($book->id);

			$this->assertEquals($book->cover->id, $attachment->id);

			// detach
			$browser->visit(route('books.edit', $book))
				->clickLink(__('book.remove_cover'))
				->with('form', function ($form) use ($attachment) {
					$this->assertNotEquals($form->attribute('img', 'src'), $attachment->fullUrlMaxSize(200, 200, 90));
				})
				->assertSee(__('attachment.cover_removed'));

			$this->assertNull($book->fresh()->cover);
		});
	}
}

