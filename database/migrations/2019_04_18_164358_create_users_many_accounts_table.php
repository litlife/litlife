<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersManyAccountsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('users_many_accounts')) {
			Schema::create('users_many_accounts', function (Blueprint $table) {
				$table->bigInteger('user_id');
				$table->bigInteger('user_id2');
				$table->integer('time');
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
		Schema::drop('users_many_accounts');
	}

}
