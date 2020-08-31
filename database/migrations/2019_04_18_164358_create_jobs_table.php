<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('jobs')) {
			Schema::create('jobs', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('queue');
				$table->text('payload');
				$table->smallInteger('attempts');
				$table->integer('reserved_at')->nullable();
				$table->integer('available_at');
				$table->integer('created_at');
				$table->index(['queue', 'reserved_at']);
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
		Schema::drop('jobs');
	}

}
