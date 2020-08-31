<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLostPasswordsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('lost_passwords')) {
			Schema::create('lost_passwords', function (Blueprint $table) {
				$table->integer('user_id')->default(0)->index('lost_password_lp_user_id');
				$table->text('code');
				$table->integer('time')->default(0);
				$table->timestamps();
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
		Schema::drop('lost_passwords');
	}

}
