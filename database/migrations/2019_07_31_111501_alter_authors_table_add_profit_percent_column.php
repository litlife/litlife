<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAuthorsTableAddProfitPercentColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('managers', function (Blueprint $table) {
			$table->tinyInteger('profit_percent')->nullable()->comment('Процент от прибыли, который получает автор');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('managers', function (Blueprint $table) {
			$table->dropColumn('profit_percent');
		});
	}
}
