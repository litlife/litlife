<?php

namespace Tests\Feature;

use Litlife\BookConverter\Facades\BookConverter;
use Litlife\Url\Url;
use Tests\TestCase;

class AbiwordConverterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConvert()
    {
        $file = BookConverter::with('abiword')
            ->open(__DIR__.'/Book/Books/test.docx')
            ->convertToFormat('doc')
            ->getFilePath();

        $this->assertTrue(file_exists($file));
        $this->assertNotEquals(0, filesize($file));
        $this->assertEquals('doc', Url::fromString($file)->getExtension());
    }
}
