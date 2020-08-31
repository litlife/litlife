<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserDatasCreateReferUsersCountColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->integer('refer_users_count')->nullable()->comment('Количество привлеченных пользователей');
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
			$table->dropColumn('refer_users_count');
		});
	}
}
