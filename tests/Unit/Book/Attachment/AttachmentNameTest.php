<?php

namespace Tests\Unit\Book\Attachment;

use App\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentNameTest extends TestCase
{
	public function testExtensionToLower()
	{
		$attachment = new Attachment();
		$attachment->name = 'имя.JPEG';

		$this->assertEquals('ima.jpeg', $attachment->name);
	}
}
