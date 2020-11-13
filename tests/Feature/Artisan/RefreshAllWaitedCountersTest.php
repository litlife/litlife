<?php

namespace Tests\Feature\Artisan;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RefreshAllWaitedCountersTest extends TestCase
{
    public function testNoErrors()
    {
        Artisan::call('refresh:all_waited_counters', ['limit' => '1', 'latest_id' => '9999999999']);

        $this->assertTrue(true);
    }
}
