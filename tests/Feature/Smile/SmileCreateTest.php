<?php

namespace Tests\Feature\Smile;

use App\Smile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmileCreateTest extends TestCase
{
    public function test()
    {
        Storage::fake(config('filesystems.default'));

        $path = __DIR__.'/../images/test.jpeg';

        $name = uniqid();
        $description = uniqid();
        $simpleForm = mb_strtoupper(uniqid());

        $smile = new Smile();
        $smile->openImage($path);
        $smile->name = $name;
        $smile->description = $description;
        $smile->simple_form = $simpleForm;
        $smile->dirname = 'smiles';
        $smile->save();

        $smile->refresh();

        $this->assertEquals('smiles', $smile->dirname);
        $this->assertEquals(mb_strtolower($simpleForm), $smile->simple_form);
        $this->assertEquals($description, $smile->description);
        $this->assertEquals($name, $smile->name);
        $this->assertEquals(604, $smile->getWidth());
        $this->assertEquals(604, $smile->getHeight());

        $this->assertTrue($smile->exists());
    }
}
