<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAddBooksPurchasedCount extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->smallInteger('books_purchased_count')->default(0)->comment('Количество книг купленных пользователем');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->dropColumn('books_purchased_count');
		});
	}
}
