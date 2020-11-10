<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use Tests\TestCase;

class AttachmentIsCoverTest extends TestCase
{
	public function testTrue()
	{
		$cover = Attachment::factory()->cover()->create();

		$this->assertTrue($cover->isCover());
	}

	public function testFalse()
	{
		$attachment = Attachment::factory()->create();

		$book = $attachment->book;

		$this->assertFalse($attachment->isCover());
	}
}
