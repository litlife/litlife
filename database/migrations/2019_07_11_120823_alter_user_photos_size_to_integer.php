<?php

use Illuminate\Database\Migrations\Migration;

class AlterUserPhotosSizeToInteger extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::table('user_photos', function (Blueprint $table) {

			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('user_photos', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table user_photos alter column size type integer using size::integer;');
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
