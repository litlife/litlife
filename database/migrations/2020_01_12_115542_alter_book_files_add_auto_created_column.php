<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookFilesAddAutoCreatedColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('book_files', function (Blueprint $table) {
			$table->boolean('auto_created')->nullable()->comment(__('book_file.auto_created'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('book_files', function (Blueprint $table) {
			$table->dropColumn('auto_created');
		});
	}
}
