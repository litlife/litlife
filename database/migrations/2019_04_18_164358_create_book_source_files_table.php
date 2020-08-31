<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookSourceFilesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_source_files')) {
			Schema::create('book_source_files', function (Blueprint $table) {
				$table->bigInteger('book_file_id');
				$table->text('source_file_name')->nullable();
				$table->text('error')->nullable();
				$table->bigInteger('failed_job_id')->nullable();
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('book_source_files');
	}

}
