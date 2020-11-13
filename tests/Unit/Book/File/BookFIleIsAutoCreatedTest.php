<?php

namespace Tests\Unit\Book\File;

use App\BookFile;
use PHPUnit\Framework\TestCase;

class BookFIleIsAutoCreatedTest extends TestCase
{
    public function testFalse()
    {
        $file = new BookFile();
        $file->auto_created = false;

        $this->assertFalse($file->isAutoCreated());
    }

    public function testTrue()
    {
        $file = new BookFile();
        $file->auto_created = true;

        $this->assertTrue($file->isAutoCreated());
    }
}
