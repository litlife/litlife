<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TextBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('text_blocks')->insertOrIgnore([
            'text' => 'Приветствие',
            'show_for_all' => false,
            'name' => 'Приветствие',
            'user_id' => 0
        ]);
    }
}
