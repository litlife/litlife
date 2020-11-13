<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        ini_set('memory_limit', '2G');
        /*
                echo(get_called_class() . '::' . $this->getName() . ' ');
        */
        $app->make(Kernel::class)->bootstrap();
        /*
                echo(' ' . round(memory_get_usage() / 1000000, 2) . ' MB');
                echo("\n");
                */
        return $app;
    }
}