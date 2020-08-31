<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserStatusTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_status')) {
			Schema::create('user_status', function (Blueprint $table) {
				$table->integer('id', true);
				$table->bigInteger('user_id')->default(0);
				$table->text('text');
				$table->integer('add_time')->default(0);
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
		Schema::drop('user_status');
	}

}
