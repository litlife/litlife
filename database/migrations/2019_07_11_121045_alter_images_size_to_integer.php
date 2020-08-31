<?php

use Illuminate\Database\Migrations\Migration;

class AlterImagesSizeToInteger extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::table('images', function (Blueprint $table) {
			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('images', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table images alter column size type integer using size::integer;');
			}
		});
		*/
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

	}
}
