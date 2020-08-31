<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserGroupsPivotAddCreatedAtColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_group_pivot', function (Blueprint $table) {
			$table->timestamp('created_at')->nullable()->comment('Время создания данных');
			$table->timestamp('updated_at')->nullable()->comment('Время обновления данных');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_group_pivot', function (Blueprint $table) {
			$table->dropColumn('created_at');
		});
	}
}
