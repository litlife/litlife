<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSizesToInteger extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_photos', function (Blueprint $table) {

			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('user_photos', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table user_photos alter column size type integer using size::integer;');
			}
		});

		Schema::table('attachments', function (Blueprint $table) {

			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('attachments', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table attachments alter column size type integer using size::integer;');
			}
		});

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

		Schema::table('images', function (Blueprint $table) {
			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('images', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table images alter column size type integer using size::integer;');
			}
		});
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
