<?php

use Illuminate\Database\Migrations\Migration;

class AlterBookFilesSizeToInteger extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::table('book_files', function (Blueprint $table) {

			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('book_files', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table book_files alter column size type integer using size::integer;');
			}

			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('book_files', 'file_size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table book_files alter column file_size type integer using file_size::integer;');
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
