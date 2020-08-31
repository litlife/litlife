<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateViewCountsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('view_counts')) {
			Schema::create('view_counts', function (Blueprint $table) {
				$table->integer('book_id', true);
				$table->bigInteger('all')->default(0);
				$table->bigInteger('year')->default(0);
				$table->integer('month')->default(0);
				$table->integer('week')->default(0);
				$table->integer('day')->default(0);
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
		Schema::drop('view_counts');
	}

}
