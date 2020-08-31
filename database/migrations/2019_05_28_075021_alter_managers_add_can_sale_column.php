<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterManagersAddCanSaleColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('managers', function (Blueprint $table) {
			$table->boolean('can_sale')->default(false)->comment('Может продавать книги или нет');
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
			$table->dropColumn('can_sale');
		});
	}
}
