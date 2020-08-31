<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDownloadCountsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('download_counts')) {
			Schema::create('download_counts', function (Blueprint $table) {
				$table->integer('bid')->default(0)->primary('pk_download_count');
				$table->integer('view_day')->default(0)->index('download_count_dc_view_day');
				$table->integer('view_week')->default(0)->index('dc_view_week_idx');
				$table->integer('view_month')->default(0)->index('dc_view_month_idx');
				$table->bigInteger('view_year')->default(0)->index('dc_view_year_idx');
				$table->bigInteger('view_all')->default(0)->index('dc_view_all_idx');
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
		Schema::drop('download_counts');
	}

}
