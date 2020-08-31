<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersOnModerationTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('users_on_moderation')) {
			Schema::create('users_on_moderation', function (Blueprint $table) {
				$table->bigInteger('user_id');
				$table->integer('time')->nullable();
				$table->integer('user_adds_id');
				$table->timestamps();
				$table->integer('id', true);
				$table->unique(['user_id', 'user_adds_id']);
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
		Schema::drop('users_on_moderation');
	}

}
