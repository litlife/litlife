<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAuthFailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_auth_fails')) {
			Schema::create('user_auth_fails', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('user_id')->index();
				$table->string('password')->nullable();
				$table->string('ip');
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('user_agent_id')->nullable();
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
		Schema::drop('user_auth_fails');
	}

}
