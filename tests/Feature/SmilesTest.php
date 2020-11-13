<?php

namespace Tests\Feature;

use App\Enums\VariablesEnum;
use App\Smile;
use App\User;
use App\Variable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmilesTest extends TestCase
{
    public function testArtisan()
    {
        Carbon::setTestNow(Carbon::create(2020, 12, 31));

        Variable::updateOrCreate(
            ['name' => VariablesEnum::SmilesJsonUrl],
            ['value' => null]
        );

        $path = Variable::where('name', VariablesEnum::SmilesJsonUrl)->first();

        $this->assertNull($path->value);

        Storage::fake(config('filesystems.default'));

        Artisan::call('smile:create_json_file');

        $this->assertTrue(Storage::exists('smiles/list.json'));

        $path = Variable::where('name', VariablesEnum::SmilesJsonUrl)->first();

        $this->assertNotNull($path->value);
        $this->assertRegExp('/smiles\/list\.json\?id\=([A-z0-9]{32})$/iu', $path->value);

        $string = Storage::get('smiles/list.json');

        preg_match('/^\/\*\*\/jsonp\((.*)\)$/iu', $string, $match);

        $array = json_decode($match[1]);

        $this->assertNotNull($array);
        $this->assertIsArray($array);
    }

    public function testIsIncludeSmilesForNewYear()
    {
        Carbon::setTestNow(Carbon::create(2020, 12, 05));

        $class = new Smile();
        $this->assertFalse($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 12, 10));

        $this->assertTrue($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 12, 31));

        $this->assertTrue($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 01, 01));

        $this->assertTrue($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 01, 10));

        $this->assertTrue($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 01, 16));

        $this->assertFalse($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 01, 30));

        $this->assertFalse($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 02, 10));

        $this->assertFalse($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 03, 20));

        $this->assertFalse($class->isIncludeSmilesForNewYear());

        Carbon::setTestNow(Carbon::create(2020, 06, 01));

        $this->assertFalse($class->isIncludeSmilesForNewYear());
    }

    public function testConsiderTimeScope()
    {
        Carbon::setTestNow(Carbon::create(2020, 01, 01));

        $smile_for_new_year = Smile::factory()->for_new_year()->create();

        $smile = Smile::factory()->create();

        $this->assertNotNull(Smile::considerTime()->find($smile_for_new_year->id));
        $this->assertNotNull(Smile::considerTime()->find($smile->id));

        Carbon::setTestNow(Carbon::create(2020, 06, 06));

        $this->assertNull(Smile::considerTime()->find($smile_for_new_year->id));
        $this->assertNotNull(Smile::considerTime()->find($smile->id));
    }

    /*
        public function testGetSmiles()
        {
            Carbon::setTestNow(Carbon::create(2020, 01, 01));

            $class = new SmilesCreateJsonFile();

            dd($class->getSmiles());
        }
        */

    public function testHomeIfSmilesUrlNotFound()
    {
        Variable::where('name', VariablesEnum::SmilesJsonUrl)
            ->delete();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk();
    }

    public function clearSmiles()
    {
        Smile::truncate();
    }
}
