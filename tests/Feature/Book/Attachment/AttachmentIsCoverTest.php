<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use Tests\TestCase;

class AttachmentIsCoverTest extends TestCase
{
	public function testTrue()
	{
		$cover = factory(Attachment::class)
			->states('cover')
			->create();

		$this->assertTrue($cover->isCover());
	}

	public function testFalse()
	{
		$attachment = factory(Attachment::class)
			->create();

		$book = $attachment->book;

		$this->assertFalse($attachment->isCover());
	}
}
