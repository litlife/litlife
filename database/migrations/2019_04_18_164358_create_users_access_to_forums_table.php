<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersAccessToForumsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('users_access_to_forums')) {
			Schema::create('users_access_to_forums', function (Blueprint $table) {
				$table->integer('user_id');
				$table->integer('forum_id');
				$table->unique(['user_id', 'forum_id']);
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
		Schema::drop('users_access_to_forums');
	}

}
