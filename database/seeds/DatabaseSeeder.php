<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::transaction(function () {
			$this->call([
				GenreSeeder::class,
				SmilesSeeder::class,
				LanguageSeeder::class,
				UserGroupSeeder::class,
				UserSeeder::class,
				ForumSeeder::class
			]);
		});
	}
}
