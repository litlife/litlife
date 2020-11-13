<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Tests\TestCase;

class AppTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test404Error()
    {
        $this->get('/'.Str::random(16))
            ->assertNotFound();
    }

    public function test401Error()
    {
        $this->get('/users')
            ->assertStatus(401);
    }

    public function test404Error2()
    {
        $this->get('/%F8')
            ->assertNotFound();
    }

    public function testClearTestingDirecrory()
    {
        $root = storage_path('framework/testing/disks/');

        $adapter = new Local($root);
        $filesystem = new Filesystem($adapter);

        $this->clearTestingDirecrory();

        $this->assertEmpty($filesystem->listContents('/', false));
    }

    public function testNoErrorIfGeoipDBNotExists()
    {
        $path = 'app/'.uniqid().'.mmdb';

        $storage_path = storage_path($path);

        $file = fopen($storage_path, 'w+');
        fclose($file);

        config(['geoip.services.maxmind_database.database_path' => $storage_path]);

        $this->get('/')
            ->assertOk();

        unlink($storage_path);
    }
}
