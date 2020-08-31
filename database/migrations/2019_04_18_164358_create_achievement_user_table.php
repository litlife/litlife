<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAchievementUserTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('achievement_user')) {
			Schema::create('achievement_user', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('user_id')->index();
				$table->integer('achievement_id');
				$table->timestamps();
				$table->integer('create_user_id');
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
		Schema::drop('achievement_user');
	}

}
