<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		dispatch(new \App\Jobs\CreateSiteAccountIfNotExists());

		\Illuminate\Support\Facades\DB::statement('ALTER SEQUENCE IF EXISTS users_id_seq RESTART WITH ' . intval(config('app.user_id') + 10) . '');
        \Illuminate\Support\Facades\DB::statement('ALTER SEQUENCE IF EXISTS user_u_id RESTART WITH ' . intval(config('app.user_id') + 10) . '');
	}
}
