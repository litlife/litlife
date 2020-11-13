<?php

namespace Tests\Feature;

use App\Library\CalibreBookConverter;
use Litlife\BookConverter\Facades\BookConverter;
use Litlife\Url\Url;
use Tests\TestCase;

class CalibreConverterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConvertFromStream()
    {
        $docx = fopen(__DIR__.'/Book/Books/test.docx', 'r+b');

        $file = BookConverter::with('calibre')
            ->open($docx, 'docx')
            ->convertToFormat('epub')
            ->getFilePath();

        $this->assertFileExists($file);
        $this->assertNotEquals(0, filesize($file));
        $this->assertEquals('epub', Url::fromString($file)->getExtension());
    }
}
