<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFailedJobsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('failed_jobs')) {
			Schema::create('failed_jobs', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->text('connection');
				$table->text('queue');
				$table->text('payload');
				$table->text('exception');
				$table->dateTime('failed_at')->default('now()');
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
		Schema::drop('failed_jobs');
	}

}
