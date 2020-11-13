<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TmpFileTest extends TestCase
{
    public function testFilePath()
    {
        $content = uniqid();

        $file = tmpfilePath($content);

        $this->assertTrue(file_exists($file));
        $this->assertEquals($content, file_get_contents($file));
    }

    public function testTmpFileRename()
    {
        $tmp = tmpfile();

        $name = stream_get_meta_data($tmp)['uri'];

        $newName = $name.'.tmp';

        rename($name, $newName);

        $this->assertFileExists($newName);

        fclose($tmp);
        unset($tmp);

        $this->assertFileExists($newName);
    }

    public function testTempName()
    {
        $name = tempnam(sys_get_temp_dir(), "FOO");

        $newName = $name.'.tmp';

        rename($name, $newName);

        $this->assertFileExists($newName);
    }
}
