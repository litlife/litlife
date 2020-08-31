<?php

use Illuminate\Database\Migrations\Migration;

class AlterBooksAddPiInfoIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_pi_city_trgm_index');
		\Illuminate\Support\Facades\DB::statement('CREATE INDEX books_pi_city_trgm_index ON books USING gin (pi_city gin_trgm_ops);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_pi_city_trgm_index');
	}
}
