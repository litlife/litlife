<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookFileDownloadLogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_file_download_logs')) {
			Schema::create('book_file_download_logs', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('book_file_id')->default(0);
				$table->integer('user_id')->nullable()->default(0);
				$table->integer('time')->nullable()->default(0);
				$table->string('ip');
				$table->timestamps();
				$table->index(['book_file_id', 'user_id', 'ip']);
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
		Schema::drop('book_file_download_logs');
	}

}
