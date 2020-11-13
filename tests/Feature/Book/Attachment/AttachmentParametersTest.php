<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use Tests\TestCase;

class AttachmentParametersTest extends TestCase
{
    public function testParameters()
    {
        $key = 'ключ';
        $value = 'значение';

        $attachment = new Attachment();
        $attachment->addParameter($key, $value);

        $this->assertEquals($value, $attachment->getParameter($key));

        $key = uniqid();

        $this->assertNull($attachment->getParameter($key));
    }
}
