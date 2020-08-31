<?php

use Illuminate\Database\Migrations\Migration;

class AlterTypeStoragesAddPrivateValue extends Migration
{
	public $withinTransaction = false;

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$result = DB::table('pg_type')->where('typname', 'storages')->get();

		if ($result->count() > 0) {
			\Illuminate\Support\Facades\DB::statement('ALTER TYPE storages ADD VALUE IF NOT EXISTS \'private\' AFTER \'null\';');

			\Illuminate\Support\Facades\DB::statement('ALTER TYPE storages ADD VALUE IF NOT EXISTS \'local\' AFTER \'private\';');
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
